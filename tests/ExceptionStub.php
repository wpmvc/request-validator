<?php

namespace WpMVC\Exceptions;

use Exception as BaseException;

/**
 * A simple stub for the framework exception since the package doesn't include the full MVC framework.
 */
class Exception extends BaseException {
    protected $messages = [];

    public function set_messages( array $messages ) {
        $this->messages = $messages;
        return $this;
    }

    public function get_messages() {
        return $this->messages;
    }
}
