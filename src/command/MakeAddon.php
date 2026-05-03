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
            ->setAliases(['addon:make', 'plugin:make'])
            ->setDescription('创建一个新插件')
            ->addArgument('name', Argument::REQUIRED, '插件名称')
            ->addOption('title', null, Option::VALUE_OPTIONAL, '插件标题')
            ->addOption('description', null, Option::VALUE_OPTIONAL, '插件描述')
            ->addOption('author', null, Option::VALUE_OPTIONAL, '插件作者')
            ->addOption('plugin-version', null, Option::VALUE_OPTIONAL, '插件版本')
            ->addOption('with-model', null, Option::VALUE_NONE, '创建模型目录')
            ->addOption('with-validate', null, Option::VALUE_NONE, '创建验证器目录')
            ->addOption('with-public', null, Option::VALUE_NONE, '创建公共资源目录');
    }

    protected function execute(Input $input, Output $output): int
    {
        $name = strtolower($input->getArgument('name'));
        
        if (!preg_match('/^[a-z][a-z0-9]*$/', $name)) {
            $output->error('插件名称只能包含小写字母和数字，且必须以字母开头');
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
        if ($pluginVersion = $input->getOption('plugin-version')) {
            $options['plugin_version'] = $pluginVersion;
        }
        
        $options['with_model'] = $input->getOption('with-model');
        $options['with_validate'] = $input->getOption('with-validate');
        $options['with_public'] = $input->getOption('with-public');

        try {
            $generator = app(Generator::class);
            $generator->create($name, $options);
            
            $output->writeln('<info>插件创建成功！</info>');
            $output->writeln('');
            $output->writeln('插件名称: ' . $name);
            $output->writeln('插件路径: ' . addons_path($name));
            $output->writeln('');
            $output->writeln('接下来你可以:');
            $output->writeln('1. 访问插件页面: addons/' . $name);
            $output->writeln('2. 管理插件: plugin/' . $name);
            
            return 0;
        } catch (\Exception $e) {
            $output->error('错误: ' . $e->getMessage());
            return 1;
        }
    }
}
