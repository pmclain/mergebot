<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\UserManagement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateAdminUserCommand extends Command
{
    /**
     * @var UserManagement
     */
    private $userManagement;

    public function __construct(
        UserManagement $userManagement
    ) {
        parent::__construct();
        $this->userManagement = $userManagement;
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Create a new admin user.');
        $this->setName('admin:user:create');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            /** @var \Symfony\Component\Console\Helper\QuestionHelper $questionHelper */
            $questionHelper = $this->getHelper('question');

            $question = new Question('<question>Email:</question>', '');
            $this->addNotEmptyValidator($question);

            $email = $questionHelper->ask($input, $output, $question);

            $question = new Question('<question>Password:</question>', '');
            $this->addNotEmptyValidator($question);
            $question->setHidden(true);

            $password = $questionHelper->ask($input, $output, $question);

            $user = $this->userManagement->createUser($email, $password);
            $output->writeln('<info>Created user: ' . $user->getUsername() . '</info>');
        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
        }
    }

    private function addNotEmptyValidator(Question $question): void
    {
        $question->setValidator(function ($value) {
            if (trim($value) == '') {
                throw new \Exception('The value cannot be empty');
            }

            return $value;
        });
    }
}
