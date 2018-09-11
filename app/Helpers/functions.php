<?php
    function secToHR($seconds) {
        $hours = number_format($seconds / 3600, 1);
        return $hours;
      }

    function secToMin($seconds) {
        $mins = number_format($seconds / 60);
        return $mins;
    }

    function getProvince($address) {
        if (!$address) {
            return null;
        }
        $province = strstr($address, '省', true);
        if (!$province) {
            $province = strstr($address, '市', true);
        }

        return $province;
    }