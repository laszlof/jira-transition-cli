<?php

namespace JiraTransition;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\RuntimeException;

use JiraRestApi\Issue\Transition;

class TransitionCommand extends JiraCommand {
  protected function configure() {
    $this->setName('transition')
      ->setDescription('Transition a jira task')
      ->setHelp('This command allows you to transition a jira task')
      ->addArgument(
        'issue',
        InputArgument::REQUIRED,
        'The issue ID to transition'
      )->addArgument(
        'transition_name',
        InputArgument::REQUIRED,
        'The name of the transition to perform'
      )->addArgument(
        'resolution',
        InputArgument::OPTIONAL,
        'Optionally add a resolution for the transition'
        );

  }
  protected function execute(InputInterface $input, OutputInterface $output) {
    $transition_name = $this->_kebabCaseToSpaces(
      $input->getArgument('transition_name')
    );
    $issue = $input->getArgument('issue');

    $output->write("Transitioning {$issue} to {$transition_name}...");

    $transition = new Transition();
    $transition->setTransitionName($transition_name);
    if (
      $input->hasArgument('resolution') &&
      ! empty($input->getArgument('resolution'))
    ) {
      $transition->fields['resolution'] = [
        'name' => $this->_kebabCaseToSpaces($input->getArgument('resolution'))
      ];
    }

    try {
      $this->_issue->transition($issue, $transition);
    } catch (\Throwable $e) {
      $output->writeln('FAILED.');
      throw new RuntimeException($e->getMessage());
    }

    $output->writeln('Success.');
  }

}
