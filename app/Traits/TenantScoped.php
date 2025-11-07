<?php

namespace App\Traits;

use App\Classes\permission;
use Illuminate\Database\Eloquent\Builder;

trait TenantScoped
{
    protected static function bootTenantScoped()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (permission::hasFullAccess()) {
                return;
            }

            $scope = permission::getScope();
            $scopeData = permission::getScopeData();

            if (!$scopeData) {
                return;
            }

            $table = $builder->getModel()->getTable();
            
            if ($scope === 'campus' && $scopeData['campus']) {
                $builder->whereExists(function ($query) use ($table, $scopeData) {
                    $query->select(\DB::raw(1))
                          ->from('tbl_campus_data')
                          ->whereColumn('tbl_campus_data.reference', $table . '.reference')
                          ->where('tbl_campus_data.campus', $scopeData['campus']);
                });
            } elseif ($scope === 'ministry' && $scopeData['ministry']) {
                $builder->whereExists(function ($query) use ($table, $scopeData) {
                    $query->select(\DB::raw(1))
                          ->from('tbl_campus_data')
                          ->whereColumn('tbl_campus_data.reference', $table . '.reference')
                          ->where('tbl_campus_data.campus', $scopeData['campus'])
                          ->where('tbl_campus_data.ministry', $scopeData['ministry']);
                });
            } elseif ($scope === 'department' && $scopeData['department']) {
                $builder->whereExists(function ($query) use ($table, $scopeData) {
                    $query->select(\DB::raw(1))
                          ->from('tbl_campus_data')
                          ->whereColumn('tbl_campus_data.reference', $table . '.reference')
                          ->where('tbl_campus_data.campus', $scopeData['campus'])
                          ->where('tbl_campus_data.ministry', $scopeData['ministry'])
                          ->where('tbl_campus_data.department', $scopeData['department']);
                });
            }
        });
    }
}
