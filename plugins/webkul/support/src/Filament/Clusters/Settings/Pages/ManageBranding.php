<?php

namespace Webkul\Support\Filament\Clusters\Settings\Pages;

use App\Support\BrandPalette;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\View\Components\ButtonComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Webkul\Support\Filament\Clusters\Settings;
use Webkul\Support\Settings\BrandSettings;

class ManageBranding extends SettingsPage
{
    use HasPageShield;

    protected static ?string $cluster = Settings::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static ?int $navigationSort = 10;

    protected static string $settings = BrandSettings::class;

    protected const COLOR_FIELDS = [
        'primary_color',
        'gray_color',
        'danger_color',
        'info_color',
        'success_color',
        'warning_color',
    ];

    protected static function getPagePermission(): ?string
    {
        return 'page_support_manage_branding';
    }

    public static function getNavigationGroup(): string
    {
        return __('support::filament/clusters/manage-branding.group');
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('support::filament/clusters/manage-branding.breadcrumb'),
        ];
    }

    public function getTitle(): string
    {
        return __('support::filament/clusters/manage-branding.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/clusters/manage-branding.title');
    }

    public function getRedirectUrl(): ?string
    {
        return static::getUrl();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('branding')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('support::filament/clusters/manage-branding.form.tabs.colors'))
                            ->icon('heroicon-o-swatch')
                            ->schema([
                                Section::make(__('support::filament/clusters/manage-branding.form.sections.colors.title'))
                                    ->description(__('support::filament/clusters/manage-branding.form.sections.colors.description'))
                                    ->columns(3)
                                    ->headerActions([
                                        Action::make('reset')
                                            ->label(__('support::filament/clusters/manage-branding.actions.reset.label'))
                                            ->icon('heroicon-o-arrow-path')
                                            ->color('gray')
                                            ->link()
                                            ->requiresConfirmation()
                                            ->action(function (Set $set): void {
                                                foreach ($this->getDefaultColors() as $field => $hex) {
                                                    $set($field, $hex);
                                                }
                                            }),
                                    ])
                                    ->schema([
                                        $this->colorField('primary_color'),

                                        $this->colorField('gray_color'),

                                        $this->colorField('danger_color'),

                                        $this->colorField('info_color'),

                                        $this->colorField('success_color'),

                                        $this->colorField('warning_color'),

                                        View::make('support::filament.clusters.settings.pages.branding-color-hint')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make(__('support::filament/clusters/manage-branding.form.sections.palettes.title'))
                                    ->description(__('support::filament/clusters/manage-branding.form.sections.palettes.description'))
                                    ->schema([
                                        View::make('support::filament.clusters.settings.pages.branding-palettes')
                                            ->viewData([
                                                'palettes' => $this->getSuggestedPalettes(),
                                                'selected' => $this->detectSelectedPalette(),
                                            ])
                                            ->columnSpanFull(),
                                    ])->columns(5),
                            ]),

                        Tab::make(__('support::filament/clusters/manage-branding.form.tabs.logo'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make(__('support::filament/clusters/manage-branding.form.sections.logo.title'))
                                    ->description(__('support::filament/clusters/manage-branding.form.sections.logo.description'))
                                    ->columns(2)
                                    ->schema([
                                        FileUpload::make('light_logo')
                                            ->label(__('support::filament/clusters/manage-branding.form.fields.light-logo'))
                                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.light-logo-helper'))
                                            ->image()
                                            ->acceptedFileTypes([
                                                'image/jpeg',
                                                'image/png',
                                                'image/gif',
                                                'image/bmp',
                                                'image/webp',
                                            ])
                                            ->disk('public')
                                            ->directory('branding')
                                            ->visibility('public'),

                                        FileUpload::make('dark_logo')
                                            ->label(__('support::filament/clusters/manage-branding.form.fields.dark-logo'))
                                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.dark-logo-helper'))
                                            ->image()
                                            ->acceptedFileTypes([
                                                'image/jpeg',
                                                'image/png',
                                                'image/gif',
                                                'image/bmp',
                                                'image/webp',
                                            ])
                                            ->disk('public')
                                            ->directory('branding')
                                            ->visibility('public'),

                                        FileUpload::make('favicon')
                                            ->label(__('support::filament/clusters/manage-branding.form.fields.favicon'))
                                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.favicon-helper'))
                                            ->image()
                                            ->acceptedFileTypes([
                                                'image/jpeg',
                                                'image/png',
                                                'image/gif',
                                                'image/bmp',
                                                'image/webp',
                                            ])
                                            ->disk('public')
                                            ->directory('branding')
                                            ->visibility('public'),

                                        TextInput::make('logo_height')
                                            ->label(__('support::filament/clusters/manage-branding.form.fields.logo-height'))
                                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.logo-height-helper'))
                                            ->placeholder('2rem'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function applyPalette(string $key): void
    {
        $palettes = $this->getSuggestedPalettes();

        if (! isset($palettes[$key])) {
            return;
        }

        foreach ($palettes[$key]['colors'] as $field => $hex) {
            $this->data[$field] = $hex;
        }
    }

    public function renderColorPreview(string $field): Htmlable|string
    {
        $colors = $this->currentColors();

        try {
            $palettes = [];

            foreach ($colors as $name => $hex) {
                $palettes[$name] = BrandPalette::fromHex($hex);
            }
        } catch (\Throwable) {
            return '';
        }

        $map = app(ButtonComponent::class)->getColorMap($palettes['primary_color']);

        $resolve = fn (array $palette, int $shade): string => $shade === 0
            ? 'oklch(1 0 0)'
            : ($palette[$shade] ?? 'oklch(1 0 0)');

        $focusShades = collect($palettes[$field])
            ->filter(fn (string $color, int $shade): bool => $shade <= 900)
            ->all();

        return new HtmlString(view('support::filament.clusters.settings.pages.branding-color-preview', [
            'focus' => [
                'key'    => $field,
                'label'  => __('support::filament/clusters/manage-branding.form.fields.'.str_replace('_', '-', $field)),
                'hex'    => $colors[$field],
                'shades' => $focusShades,
            ],
            'palettes' => $palettes,
            'button'   => [
                'bg'       => $palettes['primary_color'][$map['bg']],
                'text'     => $resolve($palettes['primary_color'], $map['text']),
                'darkBg'   => $palettes['primary_color'][$map['dark:bg']],
                'darkText' => $resolve($palettes['primary_color'], $map['dark:text']),
            ],
        ])->render());
    }

    protected function colorField(string $field): ColorPicker
    {
        return ColorPicker::make($field)
            ->label(__('support::filament/clusters/manage-branding.form.fields.'.str_replace('_', '-', $field)))
            ->hexColor()
            ->live(onBlur: true)
            ->hintAction($this->previewAction($field))
            ->extraFieldWrapperAttributes(['class' => 'fi-branding-color-card rounded-xl border border-gray-200 p-4 dark:border-white/10']);
    }

    protected function previewAction(string $field): Action
    {
        return Action::make('preview_'.$field)
            ->label(__('support::filament/clusters/manage-branding.form.preview.title'))
            ->icon('heroicon-o-information-circle')
            ->iconButton()
            ->color('gray')
            ->slideOver()
            ->modalWidth(Width::Large)
            ->modalHeading(__('support::filament/clusters/manage-branding.form.preview.heading'))
            ->modalDescription(__('support::filament/clusters/manage-branding.form.preview.description'))
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->modalContent(fn (): Htmlable|string => $this->renderColorPreview($field));
    }

    protected function currentColors(): array
    {
        $defaults = $this->getDefaultColors();

        $colors = [];

        foreach (self::COLOR_FIELDS as $field) {
            $colors[$field] = filled($this->data[$field] ?? null)
                ? $this->data[$field]
                : $defaults[$field];
        }

        return $colors;
    }

    protected function detectSelectedPalette(): ?string
    {
        $current = collect($this->currentColors())
            ->map(fn (string $hex): string => Str::lower($hex))
            ->all();

        foreach ($this->getSuggestedPalettes() as $key => $palette) {
            $colors = collect($palette['colors'])
                ->map(fn (string $hex): string => Str::lower($hex))
                ->all();

            if ($colors === $current) {
                return $key;
            }
        }

        return null;
    }

    protected function getSuggestedPalettes(): array
    {
        return [
            'coral' => [
                'name'   => __('support::filament/clusters/manage-branding.form.palettes.coral'),
                'colors' => [
                    'primary_color' => '#e74643',
                    'gray_color'    => '#52525c',
                    'danger_color'  => '#dc2626',
                    'info_color'    => '#155dfc',
                    'success_color' => '#00a63e',
                    'warning_color' => '#e17100',
                ],
            ],
            'ocean' => [
                'name'   => __('support::filament/clusters/manage-branding.form.palettes.ocean'),
                'colors' => [
                    'primary_color' => '#155dfc',
                    'gray_color'    => '#475569',
                    'danger_color'  => '#dc2626',
                    'info_color'    => '#0ea5e9',
                    'success_color' => '#059669',
                    'warning_color' => '#d97706',
                ],
            ],
            'forest' => [
                'name'   => __('support::filament/clusters/manage-branding.form.palettes.forest'),
                'colors' => [
                    'primary_color' => '#047857',
                    'gray_color'    => '#44403c',
                    'danger_color'  => '#dc2626',
                    'info_color'    => '#0284c7',
                    'success_color' => '#16a34a',
                    'warning_color' => '#ca8a04',
                ],
            ],
            'purple' => [
                'name'   => __('support::filament/clusters/manage-branding.form.palettes.purple'),
                'colors' => [
                    'primary_color' => '#7c3aed',
                    'gray_color'    => '#52525b',
                    'danger_color'  => '#e11d48',
                    'info_color'    => '#6366f1',
                    'success_color' => '#10b981',
                    'warning_color' => '#f59e0b',
                ],
            ],
            'modern' => [
                'name'   => __('support::filament/clusters/manage-branding.form.palettes.modern'),
                'colors' => [
                    'primary_color' => '#52525c',
                    'gray_color'    => '#71717a',
                    'danger_color'  => '#dc2626',
                    'info_color'    => '#2563eb',
                    'success_color' => '#16a34a',
                    'warning_color' => '#d97706',
                ],
            ],
        ];
    }

    private function getDefaultColors(): array
    {
        return [
            'primary_color' => Color::convertToHex(Color::Blue[600]),
            'gray_color'    => Color::convertToHex(Color::Zinc[600]),
            'danger_color'  => Color::convertToHex(Color::Red[600]),
            'info_color'    => Color::convertToHex(Color::Blue[600]),
            'success_color' => Color::convertToHex(Color::Green[600]),
            'warning_color' => Color::convertToHex(Color::Amber[600]),
        ];
    }
}
