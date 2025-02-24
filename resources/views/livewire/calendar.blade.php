<div class="flex max-w-2xl mx-auto h-screen items-center">
		<div class="w-full flex flex-col gap-4">
				<div class="flex gap-2 w-full">
						<x-forms.dropdown
								class="w-1/2"
								:options="$leagues"
								title="League"
								wire:model="filters.league"
								wire:change="applyFilters"
						/>

						<x-forms.dropdown
							class="w-1/2"
							:options="$teams"
							title="Team"
							wire:model="filters.team"
							wire:change="applyFilters"
						/>
				</div>

				<div class="inline-block min-w-full overflow-hidden">
						<div class="w-full flex flex-row">
								<div class="flex-1 h-12 flex items-center justify-between px-5 bg-white border text-gray-900">
										<p class="text-sm">
												<span class="font-bold text-xl">{{ $startsAt->format(format: 'F') }}</span>
												<span class="text-xl">{{ $startsAt->format(format: 'Y') }}</span>
										</p>

										<div class="flex gap-1">
												<x-button.primary wire:click="goToPreviousMonth">
														<x-icon.arrow-left />
												</x-button.primary>

												<x-button.primary wire:click="goToCurrentMonth">
														Today
												</x-button.primary>

												<x-button.primary wire:click="goToNextMonth">
														<x-icon.arrow-right />
												</x-button.primary>
										</div>
								</div>
						</div>

						<div class="w-full flex flex-row">
								@foreach($monthGrid->first() as $data)
										<div class="flex-1 h-12 -mt-px -ml-px flex items-center justify-center bg-indigo-100 text-gray-900">
												<p class="text-sm">
														{{ $data->day->format('l') }}
												</p>
										</div>
								@endforeach
						</div>

						@foreach($monthGrid as $week)
								<div class="w-full flex flex-row">
										@foreach($week as $data)
												<div class="flex-1 h-40 lg:h-12 border border-gray-200 -mt-px -ml-px">
														<div class="w-full h-full">
																<div
																		@if($data->events->isNotEmpty())
																				wire:click="onDayClick({{ $data->day->year }}, {{ $data->day->month }}, {{ $data->day->day }})"
																		@endif

																		@class([
        																'hover:bg-blue-500 hover:text-white cursor-pointer' => $data->events->isNotEmpty(),
                                        'bg-yellow-100' => $data->day->isSameMonth($startsAt) && $data->day->isToday(),
                                        'bg-white' => $data->day->isSameMonth($startsAt) && $data->day->isToday(),
                                        'bg-gray-100' => ! $data->day->isSameMonth($startsAt),
                                        'w-full h-full p-2 flex flex-col',
                                  	])
																>
																		<div class="flex items-center justify-between">
																				<p @class(['text-sm', 'font-medium' => $data->day->isSameMonth($startsAt)])>
																						{{ $data->day->format('j') }}
																				</p>
																				@if($data->events->isNotEmpty())
																						<button class="text-xs bg-red-400 text-white h-5 w-5 text-center rounded-full">
																								{{ $data->events->count() }}
																						</button>
																				@endif
																		</div>
																</div>
														</div>
												</div>
										@endforeach
								</div>
						@endforeach
				</div>
		</div>

		<x-dialog-modal x-cloack wire:model="openCurrentMatches">
				<x-slot name="title">
						Match Day for <span class="font-bold">{{ $currentDate }}</span>
				</x-slot>

				<x-slot name="content">
						<div class="overflow-y-auto h-[500px]">
								@foreach($this->currentMatches as $match)
										<div class="border rounded-lg p-4 mb-4">
												<p class="font-bold">{{ $match->league->name }}: {{ $match->match_date->format('H:i')  }}, {{ $match->location }}</p>
												<p class="text-gray-600">{{ $match->teamHome->name }} vs {{ $match->teamAway->name }}</p>
										</div>
								@endforeach
						</div>
				</x-slot>

				<x-slot name="footer">
						<x-button class="ml-3"
				          wire:click="$toggle('openCurrentMatches')"
				          wire:loading.attr="disabled"
						>
								{{ __('Ok') }}
						</x-button>
				</x-slot>
		</x-dialog-modal>
</div>