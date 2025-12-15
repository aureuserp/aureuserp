  <footer class="px-6 py-12 bg-gradient-to-r from-blue-50 via-purple-50 to-blue-100" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
		<div class="container max-w-6xl mx-auto">
			<div class="grid grid-cols-1 gap-8 md:grid-cols-3">
				<!-- Logo and Description Column -->
				<div class="md:col-span-1">
					<div class="mb-6">
						<a href="{{ url('/') }}">
							<x-filament-panels::logo />
						</a>
					</div>

					<p class="mb-4 text-gray-700 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
						{{ __('website::filament/app.footer.description') }}
					</p>

					<p class="text-gray-700 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
						{{ __('website::filament/app.footer.description_2') }}
					</p>
				</div>

				<!-- Useful Links Column -->
				<div class="md:col-span-1">
					<h3 class="mb-4 text-lg font-medium {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('website::filament/app.footer.useful_links') }}</h3>

					<ul class="space-y-2 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
						@foreach ($navigationItems as $item)
							<li>
								<a href="{{ $item->getUrl() }}" class="text-gray-700 hover:text-primary-600">
									{{ $item->getLabel() }}
								</a>
							</li>
						@endforeach
					</ul>
				</div>

				<!-- Contact and Social Media Column -->
				<div class="md:col-span-1">
					@if (isset($contacts['email']) || isset($contacts['phone']))
						<h3 class="mb-4 text-lg font-medium {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('website::filament/app.footer.contact_us') }}</h3>

						@if (isset($contacts['email']))
							<div class="mb-2">
								<a href="mailto:{{ $contacts['email'] }}" class="flex items-center text-gray-700 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'flex-row-reverse justify-end' : '' }}">
									<x-filament::icon
										icon="heroicon-m-envelope"
										class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"
									/>

									{{ $contacts['email'] }}
								</a>
							</div>
						@endif

						@if (isset($contacts['phone']))
							<div class="mb-6">
								<a href="tel:{{ $contacts['phone'] }}" class="flex items-center text-gray-700 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'flex-row-reverse justify-end' : '' }}">
									<x-filament::icon
										icon="heroicon-m-phone"
										class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"
									/>

									{{ $contacts['phone'] }}
								</a>
							</div>
						@endif
					@endif

					@if (! $socialLinks->isEmpty())
						<h3 class="mb-4 text-lg font-medium {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('website::filament/app.footer.follow_us') }}</h3>

						<div class="flex flex-wrap gap-2 {{ app()->getLocale() === 'ar' ? 'justify-end' : 'justify-start' }}">
							@foreach ($socialLinks as $item)
								<a
									href="{{ $item->getUrl() }}"
									class="p-2 text-white bg-gray-800 rounded-full hover:bg-primary-600"
									target="_blank"
								>
                                    {!! $item->getIcon() !!}
								</a>
							@endforeach
						</div>
					@endif
				</div>
			</div>

			<!-- Copyright Section -->
			<div class="flex flex-col justify-between pt-8 mt-8 border-t border-gray-200 md:flex-row {{ app()->getLocale() === 'ar' ? 'md:flex-row-reverse' : '' }}">
				<div class="text-sm text-gray-600">
					{{ __('website::filament/app.footer.copyright') }} © <a href="https://aureuserp.com/" class="text-primary-500" target="_blank">AureusERP</a>
				</div>

				<div class="mt-2 text-sm text-gray-600 md:mt-0">
					{{ __('website::filament/app.footer.powered_by') }} : <a href="https://webkul.com/" class="text-primary-500" target="_blank">Webkul Software</a>
				</div>
			</div>
		</div>
  </footer>
