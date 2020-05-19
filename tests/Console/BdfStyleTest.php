<?php

namespace Bdf\Util\Console;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;

class BdfStyleTest extends TestCase
{

    /**
     *
     */
    public function test_arguments()
    {
        $output = new BufferedOutput();
        $input = new ArrayInput(['foo' => 'bar'], new InputDefinition([
            new InputArgument('foo'),
        ]));

        $io = new BdfStyle($input, $output);

        $this->assertSame('bar', $io->argument('foo'));
    }

    /**
     *
     */
    public function test_options()
    {
        $output = new BufferedOutput();
        $input = new ArrayInput(['--bar' => 'foobar'], new InputDefinition([
            new InputOption('bar'),
        ]));

        $io = new BdfStyle($input, $output);

        $this->assertSame('foobar', $io->option('bar'));
    }

    /**
     *
     */
    public function test_basic_table()
    {
        $io = new BdfStyle(new ArrayInput([]), $output = new BufferedOutput());

        $io->table(['Foo', 'Bar'], [
            ['value', 'value'],
        ]);

        $stdout = <<<OUT
┌───────┬───────┐
│ Foo   │ Bar   │
├───────┼───────┤
│ value │ value │
└───────┴───────┘

OUT;

        $this->assertSame($stdout, $output->fetch());
    }

    /**
     *
     */
    public function test_style_table()
    {
        $io = new BdfStyle(new ArrayInput([]), $output = new BufferedOutput());
        $io->createTable(['Foo', 'Bar'], [
            ['value', 'value'],
        ], 'default')->render();

        $stdout = <<<OUT
+-------+-------+
| Foo   | Bar   |
+-------+-------+
| value | value |
+-------+-------+

OUT;

        $this->assertSame($stdout, $output->fetch());
    }

    /**
     *
     */
    public function test_style_on_table_columns()
    {
        $io = new BdfStyle(new ArrayInput([]), $output = new BufferedOutput());
        $io->createTable(['Foo', 'Bar'], [
            ['value', 'value'],
        ], 'box', [1 => 10])->render();

        $stdout = <<<OUT
┌───────┬────────────┐
│ Foo   │ Bar        │
├───────┼────────────┤
│ value │ value      │
└───────┴────────────┘

OUT;

        $this->assertSame($stdout, $output->fetch());
    }

    /**
     *
     */
    public function test_custom_table()
    {
        $io = new BdfStyle(new ArrayInput([]), $output = new BufferedOutput());
        $io->createTable(['Title'])
            ->setHeaders(['Title'])
            ->addRow(['Bar'])
            ->addRow(['Foo'])
            ->render();

        $stdout = <<<OUT
┌───────┐
│ Title │
├───────┤
│ Bar   │
│ Foo   │
└───────┘

OUT;

        $this->assertSame($stdout, $output->fetch());
    }

    /**
     *
     */
    public function test_flat()
    {
        $io = new BdfStyle(new ArrayInput([]), $output = new BufferedOutput());
        $io->flat([
            'foo' => 'bar',
            'test' => ['value', 'value'],
            'object' => new \stdClass(),
        ]);

        $stdout = <<<OUT
foo..................................... bar
test.................................... [0 => value, 1 => value]
object.................................. Object(stdClass)

OUT;

        $this->assertSame($stdout, $output->fetch());
    }

    /**
     *
     */
    public function test_inline()
    {
        $io = new BdfStyle(new ArrayInput([]), $output = new BufferedOutput());
        $io->inline('ok %s ?', 'John');
        $io->inline(' yes');

        $this->assertSame("ok John ? yes", $output->fetch());
    }

    /**
     *
     */
    public function test_currentLine()
    {
        $io = new BdfStyle(new ArrayInput([]), $output = new BufferedOutput());
        $io->currentLine(' yes');

        $this->assertSame("\r\e[2K yes", $output->fetch());
    }

    /**
     *
     */
    public function test_line()
    {
        $io = new BdfStyle(new ArrayInput([]), $output = new BufferedOutput());
        $io->line('ok %s ?', 'John');

        $this->assertSame("ok John ?\n", $output->fetch());
    }

    /**
     *
     */
    public function test_info()
    {
        $io = new BdfStyle(new ArrayInput([]), $output = new BufferedOutput());
        $io->info('ok %s ', 'John');

        $this->assertSame("ok John \n", $output->fetch());
    }
}
