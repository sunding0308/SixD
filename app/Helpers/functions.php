<?php
    function secToHR($seconds) {
        $hours = number_format($seconds / 3600, 1);
        return $hours;
      }
    function secToMin($seconds) {
        $mins = number_format($seconds / 60);
        return $mins;
    }