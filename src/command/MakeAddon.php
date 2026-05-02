<?php
declare (strict_types=1);

namespace tsaotai\addons\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use tsaotai\addons\Generator;

class MakeAddon extends Command
{
    protected function configure()
    {
        $this->setName('make:addon')
            ->setDescription('Create a new addon')
            ->addArgument('name', Argument::REQUIRED, 'Addon name')
            ->addOption('title', null, Option::VALUE_OPTIONAL, 'Addon title')
            ->addOption('description', null, Option::VALUE_OPTIONAL, 'Addon description')
            ->addOption('author', null, Option::VALUE_OPTIONAL, 'Addon author')
            ->addOption('plugin-version', null, Option::VALUE_OPTIONAL, 'Addon version')
            ->addOption('with-model', null, Option::VALUE_NONE, 'Create model directory')
            ->addOption('with-validate', null, Option::VALUE_NONE, 'Create validate directory')
            ->addOption('with-public', null, Option::VALUE_NONE, 'Create public directory');
    }

    protected function execute(Input $input, Output $output): int
    {
        $name = strtolower($input->getArgument('name'));
        
        if (!preg_match('/^[a-z][a-z0-9]*$/', $name)) {
            $output->error('Addon name must start with a letter and contain only lowercase letters and numbers');
            return 1;
        }

        $options = [];
        
        if ($title = $input->getOption('title')) {
            $options['title'] = $title;
        }
        if ($description = $input->getOption('description')) {
            $options['description'] = $description;
        }
        if ($author = $input->getOption('author')) {
            $options['author'] = $author;
        }
        if ($version = $input->getOption('plugin-version')) {
            $options['version'] = $version;
        }
        
        $options['with_model'] = $input->getOption('with-model');
        $options['with_validate'] = $input->getOption('with-validate');
        $options['with_public'] = $input->getOption('with-public');

        try {
            $generator = new Generator(app());
            $generator->create($name, $options);
            
            $output->writeln('<info>Addon created successfully!</info>');
            $output->writeln('');
            $output->writeln('Addon name: ' . $name);
            $output->writeln('Addon path: ' . addons_path($name));
            $output->writeln('');
            $output->writeln('You can now:');
            $output->writeln('1. Visit the addon at: addons/' . $name);
            $output->writeln('2. Manage the addon at: plugin/' . $name);
            
            return 0;
        } catch (\Exception $e) {
            $output->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
