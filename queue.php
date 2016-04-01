<?php

/**
*   Class representing queue of unprocessed html docs.
*/
class Queue {

    protected $queue = array();

    protected $proccessed = array();

    /**
    * Append elements to queue.
    * Only not already processed elements are appended.
    */
    public function insert($element) {
        if (!isset($this->proccessed[$element->getId()])) {
            $this->queue[] = $element;
            $this->proccessed[$element->getId()] = 1;
        }
    }

    /**
    * Return and delete next element from queue.
    * Elements in queue are hyperlinks to be proccessed.
    */
    public function get_next() {
        reset($this->queue);
        $key = key($this->queue);

        $doc = $this->queue[$key];
        unset($this->queue[$key]);
        return $doc;
    }

    /**
    *  Are there any more elements?
    */
    public function has_next() {
        return !empty($this->queue);
    }
}