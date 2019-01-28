# Description

This package implements a simple logging service that writes messages into files.

**Note**

> Please note that the LOG file is opened and closed each time a message is added to the file.
> Relatively to performance, this strategy is not the best one.
> However, it makes the management of LOG files easier.
> If you've ever faced a _dereferenced file_, then you understand why.  
> 
> In practice, a _dereferenced file_ is a file that does not show up when you list the content of a directory, although it
> really exists in the directory. And you often face this situation when you notice that the partition gets full : the
> amount of free space gets lower and lower, but the sizes of all (visible) files in the partition don't change.
> Typically, a process opens a (LOG) file and this file gets deleted while it is still opened (and written to) by the process.

# Synopsis

    use dbeurive\Log\Logger;
    
    // Available level:
    //
    // * Logger::LEVEL_FATAL
    // * Logger::LEVEL_ERROR
    // * Logger::LEVEL_WARNING
    // * Logger::LEVEL_SUCCESS
    // * Logger::LEVEL_INFO
    // * Logger::LEVEL_DATA
    // * Logger::LEVEL_DEBUG
    
    // 20190126111327 5c4c32c743390 FATAL R This is a fatal error 
    // 20190126111327 5c4c32c743390 ERROR R This is a standard error 
    
    $logger = new Logger('log-info.log', Logger::LEVEL_INFO);
    
    $logger->fatal("This is a fatal error");
    $logger->error("This is a standard error");
    $logger->warning("This is a warning");
    $logger->success("This is a success");
    $logger->info("This is a informative message");
    $logger->info("This is a multiline informative message\nother line.");
    
    $logger->data("This is a data");   // Not written
    $logger->data(array('a' => 1));    // Not written
    $logger->debug('This is a debug'); // Not written
    
    // 20190126111327 123 FATAL R This is a fatal error
    // 20190126111327 123 ERROR R This is a standard error
    
    $session_id = '123456';
    $logger = new Logger('log-info.log', Logger::LEVEL_INFO, $session_id);
    
    $logger->fatal("This is a fatal error");
    $logger->error("This is a standard error");
   
The logger produces logs that look like:

    Timestamp SessionId Level LinearizationFlag Message
    
The timestamp format is "`YYYYMMDDHHMMSS`":

* `YYYY`: four digit representation for the year.
* `MM`: two digit representation of the month (with leading zeros).
* `DD`: two-digit day of the month (with leading zeros).
* `HH`: two digit representation of the hour in 24-hour format (with leading zeros).
* `MM`: two digit representation of the minute (with leading zeros).
* `SS`: two digit representation of the second (with leading zeros).
    
If no session is specified, then the Logger constructor creates a session ID by calling `uniqid()`.

The level can be: `FATAL`, `ERROR`, `WARNING`, `INFO`, `DATA` or `DEBUG`.

The linearization flag may be `L` or `R`:

* `L`: linearized. This value indicates that the message has been linearized.
  Messages are linearised because they are made of more than one line.
  You can get the original value from the linearized one by calling the method
  `Logger::delinearize($linearized_message)`. Please note that the linearisation algorithm is the one used for [URI encoding](http://www.faqs.org/rfcs/rfc3986.html).
* `R`: Raw. This value indicates that the message has been written without modification.    

# Installation

From the command line:

    composer require dbeurive/log

From your composer.json file:

{
    "require": {
        "dbeurive/log": "*"
    }
}

# API

## Constructor

`__construct($in_path, $in_level, $in_opt_session_id=null)`

* `$in_path`: path to the LOG file.
* `$in_level` can be:
  * `Logger::LEVEL_FATAL`: only messages tagged "FATAL" will be printed to the LOG file.
  * `Logger::LEVEL_ERROR`: only messages tagged "FATAL" and "ERROR" will be printed to the LOG file.
  * `Logger::LEVEL_WARNING`: only messages tagged "FATAL", "ERROR" and "WARNING" will be printed to the LOG file.
  * `Logger::LEVEL_SUCCESS`: only messages tagged "FATAL", "ERROR", "WARNING" and "SUCCESS" will be printed to the LOG file.
  * `Logger::LEVEL_INFO`: only messages tagged "FATAL", "ERROR", "WARNING", "SUCCESS" and "INFO" will be printed to the LOG file.
  * `Logger::LEVEL_DATA`: only messages tagged "FATAL", "ERROR", "WARNING", "SUCCESS", "INFO" and "DATA" will be printed to the LOG file.
  * `Logger::LEVEL_DEBUG`: all messages will be printed to the LOG file.
* `$in_opt_session_id`: optional session ID. This string will be added to all lines of LOGs.

## Object methods

* `setNewLine(string $in_delimiter)`: set the sequence of characters used as the new line delimiter.
  The default value is "\n".
* `fatal(string $in_message)`: log a fatal error.
* `error(string $in_message)`: log ane error error.
* `warning(string $in_message)`: log a warning message.
* `success(string $in_message)`: log a success message.
* `info(string $in_message)`: log an informative message.
* `data(mixed $in_message)`: log a message (string, numerical value or boolean) or a data (array, object or resource).
* `debug(string $in_message)`: log a debug message.

## Class methods

* `needLinearization(string $in_text)`: test whether a text needs to be linearised or not.
* `linearize(string $in_text)`: linearize a given text.
* `delinearize(string $in_text)`: delinearize a given text.
* `getLevelFromName($in_level_name)`: Return the integer value that represents a given level name, identified by its name. 


The [API](src/Logger.php) is very simple, heavily documented, and you can see [this example](examples/app.php).


