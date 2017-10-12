<?php

namespace JiraTransition;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\RuntimeException;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;

class JiraCommand extends Command {

  /**
   * Jira URL (https://foobar.atlassian.net)
   * @var string
   */
  protected $_jira_url;

  /**
   * Jira username
   * @var string
   */
  protected $_jira_username;

  /**
   * Jira password
   * @var string
   */
  protected $_jira_password;

  /**
   * Jira Issue service
   * @var JiraRestApi\Issue\IssueService
   */
  protected $_issue;

  /**
   * {@inheritDoc}
   */
  protected function initialize(
    InputInterface $input,
    OutputInterface $output
  ) {
    if (
      ! getenv('JIRA_URL') ||
      ! getenv('JIRA_USERNAME') ||
      ! getenv('JIRA_PASSWORD')
    ) {
      throw new RuntimeException(
        'JIRA_URL, JIRA_USERNAME, and JIRA_PASSWORD must be set in the ' .
        'environment!'
      );
    }
    $this->_jira_url = getenv('JIRA_URL');
    $this->_jira_username = getenv('JIRA_USERNAME');
    $this->_jira_password = getenv('JIRA_PASSWORD');

    // Setup Jira API
    $config = new ArrayConfiguration([
      'jiraHost' => $this->_jira_url,
      'jiraUser' => $this->_jira_username,
      'jiraPassword' => $this->_jira_password,
      'jiraLogLevel' => 'EMERGENCY',
    ]);
    $this->_issue = new IssueService($config);

    parent::initialize($input, $output);
  }

  /**
   * Convert kebab-case to Title Case with spaces
   *
   * @param string $str
   * @return string
   */
  protected function _kebabCaseToSpaces(string $str) : string {
    return ucwords(str_replace('-', ' ', $str));
  }
}
