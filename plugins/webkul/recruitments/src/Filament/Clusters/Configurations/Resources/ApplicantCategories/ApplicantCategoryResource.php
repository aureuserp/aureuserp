<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ApplicantCategories;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ApplicantCategories\Pages\ListApplicantCategories;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ApplicantCategories\Schemas\ApplicantCategoryForm;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ApplicantCategories\Schemas\ApplicantCategoryInfolist;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ApplicantCategories\Tables\ApplicantCategoriesTable;
use Webkul\Recruitment\Models\ApplicantCategory;

class ApplicantCategoryResource extends Resource
{
    protected static ?string $model = ApplicantCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/applicant-category.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/applicant-category.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/applicant-category.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return ApplicantCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApplicantCategoriesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ApplicantCategoryInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApplicantCategories::route('/'),
        ];
    }
}
