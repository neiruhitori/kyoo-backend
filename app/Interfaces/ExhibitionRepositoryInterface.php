<?php

namespace App\Interfaces;

interface ExhibitionRepositoryInterface 
{
    public function getDailyReport($params);
    public function getMonthlyReport($params);
    public function getUnfinishedQueue($params);
    public function getFinishedQueue($params);
    public function isSameQueue($params);
    public function isSlotExceeded($params);
}