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
