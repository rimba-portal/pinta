<?php

declare(strict_types=1);

namespace Rimba\Request\Http\UI\Staff\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use UnitEnum;

class LifecyclesCatalog extends Page
{
    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedArrowPathRoundedSquare;

    protected static string|UnitEnum|null $navigationGroup =
        'Resources';

    protected static ?string $navigationLabel =
        'Lifecycles';

    protected static ?string $title =
        'Lifecycle Catalog';

    protected string $view = 'bites::lifecycles-catalog';

    protected array $definition = [];

    public function mount(): void
    {
        $this->definition = $this->loadDefinition();
    }

    public function hydrate(): void
    {
        $this->definition = $this->loadDefinition();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components(
                $this->lifecycles()
                    ->groupBy('category')
                    ->map(
                        fn (Collection $lifecycles, string $category): Section => $this->makeCategorySection(
                            category: $category,
                            lifecycles: $lifecycles,
                        )
                    )
                    ->values()
                    ->all()
            );
    }

    protected function makeCategorySection(
        string $category,
        Collection $lifecycles,
    ): Section {
        $generalLifecycles = $lifecycles
            ->filter(
                fn (array $lifecycle): bool => blank($lifecycle['parent_code_name'] ?? null)
            )
            // ->sortBy('code_name')
            ->values();

        return Section::make(str($category)->headline()->toString())
            ->extraAttributes([
                'data-catalog-section' => 'category',
            ])
            ->afterHeader($this->makeLifecycleActions($lifecycles))
            ->description(
                $this->makeCategoryDescription(
                    generalLifecycles: $generalLifecycles,
                    allLifecycles: $lifecycles,
                )
            )
            ->icon($this->getCategoryIcon($category))
            ->collapsible()
            ->persistCollapsed()
            ->schema(
                $generalLifecycles
                    ->map(
                        fn (array $generalLifecycle): Section => $this->makeGeneralLifecycleSection(
                            generalLifecycle: $generalLifecycle,
                            categoryLifecycles: $lifecycles,
                        )
                    )
                    ->all()
            );
    }

    /**
     * Create lifecycle navigation actions for a category.
     *
     * Each action links directly to the lifecycle section
     * identified by its code name.
     *
     * @return array<Action>
     */
    protected function makeLifecycleActions(
        Collection $lifecycles,
    ): array {
        return $lifecycles
            // ->sortBy('code_name')
            ->map(function (array $lifecycle): Action {
                $name = $lifecycle['name']
                    ?? 'Unnamed Lifecycle';

                $codeName = $lifecycle['code_name']
                    ?? str($name)->slug()->toString();

                $label = str($name)
                    ->replaceEnd(' Lifecycle', '')
                    ->toString();

                $isGeneral = blank(
                    $lifecycle['parent_code_name'] ?? null
                );

                return Action::make("goto_{$codeName}")
                    ->label($label)
                    ->badge()
                    ->color($isGeneral ? 'primary' : 'gray')
                    ->outlined(! $isGeneral)
                    ->size('sm')
                    ->url("#lifecycle-{$codeName}")
                    ->extraAttributes([
                        'x-on:click' => $this->makeLifecycleNavigationScript(
                            $codeName
                        ),
                    ]);
            })
            ->values()
            ->all();
    }

    protected function makeLifecycleNavigationScript(
        string $codeName,
    ): string {
        $targetId = "lifecycle-{$codeName}";

        return <<<JS
        \$event.preventDefault();
        \$event.stopPropagation();
        \$event.stopImmediatePropagation();

        const target = document.getElementById('{$targetId}');

        if (! target) {
            return;
        }

        const sections = [];
        let currentSection = target;

        while (currentSection) {
            if (
                currentSection.matches?.(
                    '[data-catalog-section]'
                )
            ) {
                sections.unshift(currentSection);
            }

            currentSection = currentSection.parentElement
                ?.closest('[data-catalog-section]');
        }

        sections.forEach((section) => {
            const state = Alpine.\$data(section);

            if (
                state
                && ('isCollapsed' in state)
            ) {
                state.isCollapsed = false;
            }
        });

        window.history.replaceState(
            null,
            '',
            '#{$targetId}'
        );

        setTimeout(() => {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
            });
        }, 150);
    JS;
    }

    protected function makeGeneralLifecycleSection(
        array $generalLifecycle,
        Collection $categoryLifecycles,
    ): Section {
        $codeName = $generalLifecycle['code_name']
            ?? str(
                $generalLifecycle['name']
                    ?? 'lifecycle'
            )
                ->slug()
                ->toString();

        $specificLifecycles = $categoryLifecycles
            ->filter(
                fn (array $lifecycle): bool => ($lifecycle['parent_code_name'] ?? null)
                    === $codeName
            )
            // ->sortBy('code_name')
            ->values();

        return Section::make(
            $generalLifecycle['name']
                ?? 'Unnamed Lifecycle'
        )
            ->id("lifecycle-{$codeName}")
            ->extraAttributes([
                'data-catalog-section' => 'general',
                'data-lifecycle-code' => $codeName,
            ])
            ->description(
                $this->makeLifecycleSubtitle(
                    lifecycle: $generalLifecycle,
                    specificLifecycleCount: $specificLifecycles->count(),
                )
            )
            ->icon(
                Heroicon::OutlinedArrowsRightLeft
            )
            ->collapsible()
            ->persistCollapsed()
            ->schema([
                ...$this->makeLifecycleOverviewComponents(
                    lifecycle: $generalLifecycle,
                ),

                ...$specificLifecycles
                    ->map(
                        fn (array $specificLifecycle): Section => $this->makeSpecificLifecycleSection(
                            lifecycle: $specificLifecycle,
                        )
                    )
                    ->all(),
            ]);
    }

    protected function makeSpecificLifecycleSection(
        array $lifecycle,
    ): Section {
        $name = $lifecycle['name']
            ?? 'Unnamed Lifecycle';

        $codeName = $lifecycle['code_name']
            ?? str($name)
                ->slug()
                ->toString();

        return Section::make($name)
            ->id("lifecycle-{$codeName}")
            ->extraAttributes([
                'data-catalog-section' => 'specific',
                'data-lifecycle-code' => $codeName,
            ])
            ->description(
                $this->makeLifecycleSubtitle($lifecycle)
            )
            ->icon(
                Heroicon::OutlinedArrowTurnDownRight
            )
            ->collapsible()
            ->collapsed()
            ->secondary()
            ->schema(
                $this->makeLifecycleOverviewComponents(
                    lifecycle: $lifecycle,
                )
            );
    }

    protected function makeLifecycleOverviewComponents(
        array $lifecycle,
    ): array {
        $components = [];

        $phases = $this->phases($lifecycle);

        if ($phases->isEmpty()) {
            $components[] =
                Section::make('Lifecycle phases')
                    ->description(
                        'No phase definitions are currently '
                            .'available for this lifecycle.'
                    )
                    ->icon(
                        Heroicon::OutlinedInformationCircle
                    )
                    ->compact()
                    ->schema([
                        Text::make(
                            'Phase data has not been defined.'
                        ),
                    ]);

            return $components;
        }

        $components[] = $this->makeLifecycleWizard(
            lifecycle: $lifecycle,
            phases: $phases,
        );

        return $components;
    }

    protected function makeLifecycleWizard(
        array $lifecycle,
        Collection $phases,
    ): Wizard {
        $codeName = $lifecycle['code_name']
            ?? str(
                $lifecycle['name']
                    ?? 'lifecycle'
            )
                ->slug()
                ->toString();

        return Wizard::make(
            $phases
                ->map(
                    fn (array $phase): Step => $this->makePhaseStep(
                        lifecycle: $lifecycle,
                        phase: $phase,
                    )
                )
                ->all()
        )
            ->skippable()
            ->persistStepInQueryString(
                key: "lifecycle-{$codeName}",
            );
    }

    protected function makePhaseStep(
        array $lifecycle,
        array $phase,
    ): Step {
        $phaseName = $phase['name']
            ?? 'Unnamed Phase';

        $functionalRole =
            $phase['functional_role']
            ?? 'Not defined';

        $description =
            $phase['model_state_description']
            ?? 'No model state description '
            .'has been defined.';

        return Step::make($phaseName)
            ->completedIcon(
                Heroicon::OutlinedCheckCircle
            )
            ->schema([
                Callout::make(
                    'Done by '.$functionalRole
                )
                    ->description($description)
                    ->info(),
            ]);
    }

    protected function makeCategoryDescription(
        Collection $generalLifecycles,
        Collection $allLifecycles,
    ): string {
        $generalCount =
            $generalLifecycles->count();

        $specificCount =
            $allLifecycles->count()
            - $generalCount;

        return sprintf(
            'Lifecycle%s : %d general · %d specific',
            $specificCount + $generalCount === 1
                ? ''
                : 's',
            $generalCount,
            $specificCount,
        );
    }

    protected function makeLifecycleSubtitle(
        array $lifecycle,
        ?int $specificLifecycleCount = null,
    ): string {
        $definition =
            $lifecycle['search_definition']
            ?? 'No definition';

        $parts = [
            $definition,
        ];

        if ($specificLifecycleCount > 0) {
            $parts[] =
                "{$specificLifecycleCount} "
                .str('specific lifecycle')
                    ->plural(
                        $specificLifecycleCount
                    );
        }

        return implode(' · ', $parts);
    }

    protected function phases(
        array $lifecycle,
    ): Collection {
        return collect(
            $lifecycle['phases'] ?? []
        )
            ->sortBy(
                fn (array $phase): int => (int) (
                    $phase['sequence'] ?? 0
                )
            )
            ->values();
    }

    protected function lifecycles(): Collection
    {
        return collect(
            $this->definition['lifecycles']
                ?? []
        )
            ->filter(
                fn (mixed $lifecycle): bool => is_array($lifecycle)
            )
            ->values();
    }

    protected function getCategoryIcon(
        string $category,
    ): Heroicon {
        return match ($category) {
            'governance' => Heroicon::OutlinedScale,

            'information' => Heroicon::OutlinedDocumentText,

            'workforce' => Heroicon::OutlinedUserGroup,

            'product' => Heroicon::OutlinedCube,

            'fulfilment' => Heroicon::OutlinedInboxArrowDown,

            'operations' => Heroicon::OutlinedCog6Tooth,

            'asset' => Heroicon::OutlinedWrenchScrewdriver,

            'supply' => Heroicon::OutlinedTruck,

            'relationship' => Heroicon::OutlinedLink,

            default => Heroicon::OutlinedRectangleGroup,
        };
    }

    protected function loadDefinition(): array
    {
        $path = config(
            'bites.kerja.lifecycle_catalog_path',
            'feed/lifecycles.json',
        );

        if (! Storage::disk('local')->exists($path)) {
            throw new RuntimeException(
                "Lifecycle catalog [{$path}] "
                    .'was not found on the local '
                    .'storage disk.'
            );
        }

        $decoded = json_decode(
            Storage::disk('local')->get($path),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        if (! is_array($decoded)) {
            throw new RuntimeException(
                'Lifecycle catalog must contain '
                    .'a JSON object.'
            );
        }

        if (
            ! isset($decoded['lifecycles'])
            || ! is_array(
                $decoded['lifecycles']
            )
        ) {
            throw new RuntimeException(
                'Lifecycle catalog must contain a '
                    .'top-level [lifecycles] array.'
            );
        }

        return $decoded;
    }
}
