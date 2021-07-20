<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\SchoolApiService;
use Twig\Environment;

class ContentGenerateCardsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'content:generate:cards';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Generate school cards.';

    /**
     * @var SchoolApiService
     */
    private $schoolApiService;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(SchoolApiService $schoolApiService, Environment $twig)
    {
        $this->schoolApiService = $schoolApiService;
        $this->twig = $twig;
        parent::__construct();
    }

    /**
     * Page listing.
     *
     * @return array
     */
    private function listings(): array {
        return [
            [
                'name' => 'School Directory',
                'url' => '#direct',
                'description' => 'Browse and list all HCPSS schools. Quickly
                    access each school\'s phone number, building address,
                    website, school opening and closing times, and school profiles.',
            ],[
                'name' => 'School Locator',
                'url' => '/school-locator/',
                'description' => 'Use our online map application to locate your address within the county,
                    find your planning polygon number, and view adopted attendance areas.',
            ],[
                'name' => 'School Transportation',
                'url' => 'https://www.hcpss.org/schools/transportation/',
                'description' => 'Find a link to our bus stop locator, and learn more about transportation
                    safety and emergency closing information.',
            ],[
                'name' => 'Community Superintendents',
                'url' => '/contact-us/community-superintendents/',
                'description' => 'Browse the list of Community Superintendents and their assigned schools.',
            ],[
                'name' => 'Board Member Cluster Assignments',
                'url' => 'https://www.boarddocs.com/mabe/hcpssmd/Board.nsf/goto?open&amp;id=84SRLW6E9136',
                'description' => 'Individual Board members are assigned to specific schools visit, attend
                    special events, and become additional points of contact for each school
                    community.',
            ],[
                'name' => 'School Planning',
                'url' => '/school-planning/',
                'description' => 'Get more information about projected student enrollment, school boundary
                    studies, attendance area adjustments, and our annual feasibility study.',
            ],[
                'name' => 'Emergency Closings',
                'url' => '/schools/emergency-closings/',
                'description' => 'Find emergency closing information and learn more about how decisions are made.',
            ],[
                'name' => 'School Opening and Closing Times',
                'url' => '/schools/opening-and-closing-times/',
                'description' => 'View the opening and closing times for all schools and education centers.',
            ],[
                'name' => 'Use of School Facilities',
                'url' => '/schools/facilities/',
                'description' => 'View information and deadlines, and complete an online request for the
                    use of one of our facilities.',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $schools = array_values($this->schoolApiService->getSchools());
        usort($schools, function ($a, $b) {
            return $a['full_name'] <=> $b['full_name'];
        });

        $locations = array_reduce($schools, function ($carry, $item) {
            if (!empty($item['address']['city']) && !in_array($item['address']['city'], $carry)) {
                $carry[] = $item['address']['city'];
            }
            return $carry;
        }, []);
        sort($locations);

        $levels = [
            'es' => ['name' => 'Elementary School', 'count' => 0],
            'ms' => ['name' => 'Middle School', 'count' => 0],
            'hs' => ['name' => 'High School', 'count' => 0],
            'ec' => ['name' => 'Education Center', 'count' => 0],
        ];
        foreach ($schools as $school) {
            $levels[$school['level']]['count']++;
        }

        echo $this->twig->render('cards.html.twig', [
            'schools' => $schools,
            'locations' => $locations,
            'levels' => $levels,
            'listings' => $this->listings(),
            'awards' => $this->schoolApiService->getAwards(),
        ]);

        return Command::SUCCESS;
    }
}
