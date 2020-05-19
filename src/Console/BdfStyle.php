<?php

namespace Bdf\Util\Console;

use Bdf\Util\Arr;
use Bdf\Util\ValueExporter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * BdfStyle
 */
class BdfStyle extends SymfonyStyle
{
    private $input;
    private $output;

    /**
     * BdfStyle constructor.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($input, $output);

        $this->input = $input;
        $this->output = $output;

        $formatter = $this->output->getFormatter();
        if (!$formatter->hasStyle('debug')) {
            $formatter->setStyle('debug', new OutputFormatterStyle('black'));
        }
        if (!$formatter->hasStyle('alert')) {
            $formatter->setStyle('alert', new OutputFormatterStyle('red'));
        }
    }

    //-------- output

    /**
     * Format input to textual table
     *
     * @param array|\Closure $headers  Could be a closure for custom table. Default style are set.
     * @param array  $rows
     * @param string $style
     * @param array  $columnWidths
     *
     * @return Table
     */
    public function createTable(array $headers, array $rows = [], $style = 'box', array $columnWidths = []): Table
    {
        $table = new Table($this);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->setStyle($style);

        foreach ($columnWidths as $column => $width) {
            $table->setColumnWidth($column, $width);
        }

        return $table;
    }

    /**
     * Format input to textual table
     *
     * @param array $headers  Could be a closure for custom table. Default style are set.
     * @param array  $rows
     */
    public function table(array $headers, array $rows = [])
    {
        $this->createTable($headers, $rows)->render();
    }

    /**
     * Formats a horizontal table.
     *
     * @param array $headers
     * @param array $rows
     */
    public function horizontalTable(array $headers, array $rows)
    {
        $this->createTable($headers, $rows)->setHorizontal(true)->render();
    }

    /**
     * Render a flat array
     *
     * @param array  $data
     * @param int    $maxLen
     * @param string $separator
     */
    public function flat(array $data, $maxLen = 40, $separator = '.')
    {
        foreach (Arr::dot($data) as $key => $value) {
            $this->line('%s %s', str_pad($key, $maxLen, $separator), ValueExporter::export($value));
        }
    }

    /**
     * Write a string as inline standard output.
     *
     * @param string $string
     * @param array $parameters
     */
    public function inline($string, ...$parameters)
    {
        $this->output->write($this->prepareOutputText($string, $parameters));
    }

    /**
     * Remove the current line and write a string as standard output.
     *
     * @param string $string
     * @param array $parameters
     */
    public function currentLine($string, ...$parameters)
    {
        $this->inline("\r\033[2K$string", ...$parameters);
    }

    /**
     * Write a string as standard output.
     *
     * @param string $string
     * @param array $parameters
     */
    public function line($string, ...$parameters)
    {
        $this->output->writeln($this->prepareOutputText($string, $parameters));
    }

    /**
     * Add additionnal parameters into the string
     *
     * @param string $string
     * @param array $parameters  parameters of vsprintf
     *
     * @return string
     */
    private function prepareOutputText($string, $parameters)
    {
        if (empty($parameters)) {
            return $string;
        }

        return vsprintf($string, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function success($message, ...$parameters)
    {
        parent::success($this->prepareOutputText($message, $parameters));
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, ...$parameters)
    {
        parent::error($this->prepareOutputText($message, $parameters));
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, ...$parameters)
    {
        parent::warning($this->prepareOutputText($message, $parameters));
    }

    /**
     * {@inheritdoc}
     */
    public function note($message, ...$parameters)
    {
        parent::note($this->prepareOutputText($message, $parameters));
    }

    /**
     * {@inheritdoc}
     */
    public function caution($message, ...$parameters)
    {
        parent::caution($this->prepareOutputText($message, $parameters));
    }

    /**
     * Write a string as debug output. Check verbosity before output
     *
     * @param string $string
     * @param int $verbosity
     */
    public function debug($string, $verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE)
    {
        if ($this->output->getVerbosity() >= $verbosity) {
            $this->output->writeln("<debug>$string</debug>");
        }
    }

    /**
     * Write a string as information output.
     *
     * @param string $string
     * @param array $parameters
     */
    public function info($string, ...$parameters)
    {
        $this->line("<info>$string</info>", ...$parameters);
    }

    /**
     * Write a string as comment output.
     *
     * @param string $string
     * @param array $parameters
     */
    public function comment($string, ...$parameters)
    {
        $this->line("<comment>$string</comment>", ...$parameters);
    }

    /**
     * Write a string as alert output.
     *
     * @param string $string
     * @param array $parameters
     */
    public function alert($string, ...$parameters)
    {
        $this->line("<alert>$string</alert>", ...$parameters);
    }

    //-------- input

    /**
     * Get the value of a command argument.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function argument(string $key)
    {
        return $this->input->getArgument($key);
    }

    /**
     * Get the value of a command option.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function option(string $key)
    {
        return $this->input->getOption($key);
    }

    /**
     * Prompt the user for input.
     *
     * @param string   $question
     * @param array    $choices
     * @param string   $default
     *
     * @return string
     */
    public function askWithCompletion($question, array $choices, $default = null)
    {
        $question = new Question($question, $default);
        $question->setAutocompleterValues($choices);

        return $this->askQuestion($question);
    }
}
