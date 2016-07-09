<?php

declare (strict_types = 1);

namespace TwitterKeywordFrequency\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use tests\fixtures\FakeTwitterKeywordFrequencyProjection;
use TwitterKeywordFrequency\Application\Projection\TwitterKeywordFrequencyProjection;
use TwitterKeywordFrequency\CLI\Renderer\TwitterKeywordFrequencyRenderer;

class TwitterKeywordFrequencyCommand extends Command
{
    /** {@inheritdoc} */
    protected function configure()
    {
        $this
            ->setName('twitter:keyword:frequency')
            ->setDescription('Reads frequency of keywords in last 100 tweets from a given account')
            ->addArgument('account', InputArgument::REQUIRED);
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $account = $input->getArgument('account');

        try {
            $this->renderer()->render($account, $output);
        } catch (\Exception $e) {
            return $this->handleException($e, $output);
        }

        return 0;
    }

    private function handleException(\Exception $exception, OutputInterface $output) : int
    {
        $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));

        return 1;
    }

    private function renderer() : TwitterKeywordFrequencyRenderer
    {
        return new TwitterKeywordFrequencyRenderer($this->projection());
    }

    private function projection() : TwitterKeywordFrequencyProjection
    {
        if ($this->getApplication()->getVersion() == 'debug') {
            return new FakeTwitterKeywordFrequencyProjection();
        }
    }
}
