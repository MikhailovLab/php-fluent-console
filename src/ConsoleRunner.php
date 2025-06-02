<?php

/**
* Command line command execution class with opcode support.
* This class helps to launch command processes and manage their output.
* and support working with different encodings.
* Carefully check the input data, this can lead to uncontrolled input.
* to security issues.
*_______________________________________________________________________
* Класс для выполнения команд в командной строке с поддержкой кодировок.
* Этот класс помогает запускать командные процессы, управлять их выводом
* и поддерживать работу с различными кодировками.
* Внимательно проверяйте входные данные, неконтролируемый ввод может привести
* к проблемам с безопасностью.
*/

namespace FluentConsole;

class ConsoleRunner
{
    /**
	 * Command line.
     * Строка команды.
     * @var string
     */
    private string $command = '';
    
    /**
	 * Command output.
     * Вывод команды.
     * @var array
     */
    private array $output = [];
    
    /**
	 * Return code after executing the command.
     * Код возврата после выполнения команды.
     * @var int
     */
    private int $returnCode = 0;
    
    /**
	 * Output encoding.
     * Кодировка вывода.
     * @var ?string
     */
    private ?string $encoding = null;
    
    /**
	 * List of available encodings.
     * Список доступных кодировок.
     * @var array
     */
    private array $encodings = [
        '866' => 'CP866',
        '1251' => 'CP1251',
        '65001' => 'UTF-8',
        '437' => 'CP437',
        '1252' => 'CP1252'
    ];

    /**
	 * Flag to determine whether the output should be converted.
     * Флаг для определения необходимости конвертирования вывода.
     * @var bool
     */
    private bool $decoding = false;

    /**
	 * Sets the command to execute.
     * Устанавливает команду для выполнения.
     * 
     * @param string Command | Команда
     * @return $this
     */
    public function setCommand(string $cmd): self
    {
        $this->command = $cmd;
        return $this;
    }

    /**
	 * Adds an argument or part of a command.
	 * Empty arguments are ignored.
     * Добавляет аргумент или часть команды.
     * Пустые аргументы игнорируются.
     * 
     * @param string $key Argument | Аргумент
     * @return $this
     */
    public function addKey(string $key): self
    {
        if ($key !== '') {
            $this->command .= escapeshellarg(' ' . $key);
        }
        return $this;
    }

    /**
	 * Sets the encoding for output.
	 * For example, '866' to display Cyrillic in Windows.
     * Устанавливает кодировку для вывода.
     * Например, '866' для отображения кириллицы в Windows.
     * 
     * @param ?string $encoding Encoding (eg '866') | Кодировка (например, '866')
     * @return $this
     */
    public function encoding(?string $encoding = null): self
    {
        if ($encoding) {
            $this->encoding = $encoding;
        }
        return $this;
    }

    /**
	 * Sets a flag that the encoding should be converted back.
	 * The method is useful for returning the output in the original encoding for working with cmd.
     * Устанавливает флаг, что нужно выполнить обратную конвертацию кодировки.
     * Метод полезен для возврата вывода в исходной кодировке для работы с cmd.
     * 
     * @return $this
     */
    public function decoding(): self
    {
        $this->decoding = true;
        return $this;
    }

    /**
	 * Returns the current command.
     * Возвращает текущую команду.
     * 
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
	 * Executes the command and returns true if the return code is 0.
     * Выполняет команду и возвращает true, если код возврата равен 0.
     * 
     * @return bool
     */
    public function run(): bool
    {
        $encoding = '';

		// Add encoding for Windows if installed
        // Добавляем кодировку для Windows, если она установлена
        if ($this->encoding) {
            $encoding = 'chcp ' . $this->encoding . ' >nul  && ';
        }

        exec($encoding . $this->command . ' 2>&1', $this->output, $this->returnCode);

        return $this->returnCode === 0;
    }

    /**
	 * Get the output after executing the command.
     * Получить вывод после выполнения команды.
     * 
     * @return array
     */
    public function getOutput(): array
    {
        return $this->output;
    }

    /**
	 * Get the return code of the command execution.
     * Получить код возврата выполнения команды.
     * 
     * @return int
     */
    public function getReturnCode(): int
    {
        return $this->returnCode;
    }

    /**
	 * Checks output for errors using a regular expression.
     * Проверяет вывод на наличие ошибки по регулярному выражению.
     * 
     * @param string $pattern Регулярное выражение для поиска 
	 * 						  Regular expression for search
     * @return bool
     */
    public function hasError(string $pattern): bool
    {
        foreach ($this->output as $line) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }
        return false;
    }

    /**
	 * Gets all output lines that match a regular expression.
     * Получает все строки вывода, совпавшие с регулярным выражением.
     * 
     * @param string|array $patterns Regular expression or array of expressions to search for 
	 * 							  	 Регулярное выражение или массив выражений для поиска
     * @return array
     */
    public function getMatches(string|array $patterns): array
    {
		// If necessary, convert the output to UTF-8
        // Если необходимо, конвертируем вывод в UTF-8
        if ($this->encoding) {
            $this->output = array_map(function($line) {
                return mb_convert_encoding($line, 'UTF-8', $this->encodings[$this->encoding]);
            }, $this->output);
        }

        $patterns = (array) $patterns;
        $matches = [];

		// We go through the output and look for matches
        // Проходим по выводу и ищем совпадения
        foreach ($this->output as $line) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $line, $m)) {
                    $matches[] = count($m) > 1 ? $m[1] : $m[0];
                    break; // Stop at the first matching pattern
						   // Останавливаемся на первом совпавшем паттерне
                }
            }
        }

		// If you want to return the output in the original encoding
        // Если нужно вернуть вывод в исходной кодировке
        if ($this->encoding && $this->decoding) {
            $matches = array_map(function($line) {
                return mb_convert_encoding($line, $this->encodings[$this->encoding], 'UTF-8');
            }, $matches);
        }

        return $matches;
    }
}
