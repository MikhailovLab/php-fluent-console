# PhpFluentConsole

**PhpFluentConsole** is a command line library for working with a fluent interface. It provides flexible capabilities for creating and executing command processes, as well as support for various encodings, such as CP866, CP1251, UTF-8 and others, which is especially useful when working with Cyrillic in Windows. The library also includes a system for filtering arguments, as well as capabilities for flexible output parsing using regular expressions.

**PhpFluentConsole** — это библиотека для работы с командной строкой через текучий интерфейс. Она предоставляет гибкие возможности для создания и выполнения командных процессов, а также поддержку различных кодировок, таких как CP866, CP1251, UTF-8 и другие, что особенно полезно при работе с кириллицей в Windows. Библиотека также включает систему фильтрации аргументов, а также возможности для гибкого парсинга вывода с использованием регулярных выражений.

---
## Features

- **Flexible command construction**: The library allows you to dynamically generate commands via a fluent interface, which makes it easily extensible and suitable for creating other libraries or services on top of it.

- **Regular expressions for output**: You can filter command output using regular expressions to search for specific strings or to identify errors in the output.

- **Encoding support**: The library supports various output encodings, including Windows encodings such as CP866 and CP1251, which is useful for handling Cyrillic in the command line correctly.

- **Flexible error handling**: You can customize the error output to suit your requirements - either output the command return code or search for errors using regular expressions.

- **Security**: Carefully check the input data, as uncontrolled input can lead to security issues, such as command injection.

## Особенности

- **Гибкое построение команд**: Библиотека позволяет динамически генерировать команды через текучий интерфейс, что делает ее легко расширяемой и подходящей для создания других библиотек или сервисов поверх нее.
  
- **Регулярные выражения для вывода**: Вы можете фильтровать вывод команд с использованием регулярных выражений для поиска конкретных строк или для определения ошибок в выводе.

- **Поддержка кодировок**: Библиотека поддерживает различные кодировки вывода, включая кодировки для Windows, такие как CP866 и CP1251, что полезно для корректной работы с кириллицей в командной строке.

- **Гибкая обработка ошибок**: Вы можете настроить вывод ошибок, чтобы он соответствовал вашим требованиям — либо выводить код возврата команды, либо искать ошибки с помощью регулярных выражений.

- **Безопасность**: Внимательно проверяйте входные данные, так как неконтролируемый ввод может привести к проблемам безопасности, например, к инъекциям команд.

---

## Installation

## Установка

```cmd/bash
composer require mikhailovlab/php-fluent-console

```
## Method descriptions
## Описание методов 

* Sets the command to execute.
* Устанавливает команду для выполнения.
```php 
public function setCommand(string $cmd): self
```

* Adds an argument or part of a command.
* Добавляет аргумент или часть команды.
```php 
public function addKey(string $key): self
```

* Sets the encoding for output. For example, '866' to display Cyrillic in Windows.
* Устанавливает кодировку для вывода. Например, '866' для отображения кириллицы в Windows.
```php
public function encoding(?string $encoding = null): self
```

* Sets a flag that the encoding should be converted back. The method is useful for returning the output in the original encoding for working with cmd.
* Устанавливает флаг, что нужно выполнить обратную конвертацию кодировки. Метод полезен для возврата вывода в исходной кодировке для работы с cmd.
```php
public function decoding(): self
```

* Returns the current command.
* Возвращает текущую команду.
```php
public function getCommand(): string
```

* Executes the command and returns true if the return code is 0.
* Выполняет команду и возвращает true, если код возврата равен 0.
```php
public function run(): bool
```

* Get the output after executing the command.
* Получить вывод после выполнения команды.
```php
public function getOutput(): array
```

* Get the return code of the command execution.
* Получить код возврата выполнения команды.
```php
public function getReturnCode(): int
```

* Checks output for errors using a regular expression.
* Проверяет вывод на наличие ошибки по регулярному выражению.
```php
public function hasError(string $pattern): bool
```

* Gets all output lines that match a regular expression.
* Получает все строки вывода, совпавшие с регулярным выражением.
```php
public function getMatches(string|array $patterns): array
```

#### Example 1
* Getting an IP address in the Windows operating system
* Получаем ip адерс в операционной системе windows

```php 
$cli = new ConsoleRunner();
$cli->setCommand('ipconfig')
    ->addKey('/all');

if ($cli->run()) {
    print_r($cli->getOutput());
    exit();
}

exit('Error, code: ' . $cli->getReturnCode());
```

#### Example 2
* List digital signature containers (CSP)
* Список контейнеров с электронными подписями
```php 
$cli = new ConsoleRunner();
$cli->setCommand('csptest')
    ->addKey('-keyset')
    ->addKey('-enum_cont')
    ->addKey('-verifycontext')
    ->addKey('-fqcn');

if ($cli->run()) {
    // Filter output lines matching the pattern (e.g., container names)
    print_r($cli->getMatches('#\\\\.*#'));
    exit();
}

$pattern = '/\[ErrorCode:\s*(0x[0-9A-Fa-f]+)\]/';
exit('Error code: ' . $cli->getMatches($pattern)[0]);
```

#### Example 3
* We can inherit from the main class and instead of specifying methods via addKey we can call them dynamically using the magic method __call
* Мы можем наследоваться от главного класса и вместо указания методов через addKey вызывать их динамически используя магический метод __call
```php 
class customRunner extends ConsoleRunner
{
    private $methods = [
        'keyset',
        'enum_cont',
        'verifycontext',
        'fqcn'
    ];

    public function __call(string $name, array $arguments): self
    {
        if (in_array($name, $this->methods)) {
            $this->addKey('-' . $name);

            if (!empty($arguments)) {
                foreach ($arguments as $arg) {
                    $this->addKey((string) $arg);
                }
            }

            return $this;
        }

        throw new \BadMethodCallException("Method $name is not supported");
    }

}


try{
    $cli = new customRunner()
        ->setCommand('csptest')
        ->keyset()
        ->enum_cont() 
        ->verifycontext()
        ->fqcn();

    if ($cli->run()) {
        print_r($cli->getMatches('#\\\\.*#'));
        exit();
    }

    $pattern = '/\[ErrorCode:\s*(0x[0-9A-Fa-f]+)\]/';
    exit('Error code: ' . $cli->getMatches($pattern)[0]);

}catch (Exception $e){
    exit($e->getMessage());
}
```

* This approach allows us to create flexible bindings and libraries without having to study the documentation.
* Такой подход позволяет нам создавать гибкие обвязки и библиотеки, не отвлекаясь на изучение документации.
