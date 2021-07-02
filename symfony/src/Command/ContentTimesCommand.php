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

class ContentTimesCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'content:generate:times';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Generate the school start and stop times page.';

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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $schools = array_values($this->schoolApiService->getSchools());
        usort($schools, function ($a, $b) {
            return $a['full_name'] <=> $b['full_name'];
        });

        $levels = [
            'es' => ['name' => 'Elementary Schools'],
            'ms' => ['name' => 'Middle Schools'],
            'hs' => ['name' => 'High Schools'],
            'ec' => ['name' => 'Special Schools & Education Centers'],
        ];

        foreach ($levels as $code => $level) {
            $levels[$code]['schools'] = array_filter($schools, function ($school) use ($code)  {
                return $school['level'] == $code;
            });
        }

        echo $this->twig->render('times.html.twig', [
            'levels' => $levels,
        ]);

        return Command::SUCCESS;
    }
}
