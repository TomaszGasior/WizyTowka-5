<?php

/**
* WizyTówka 5
* Common code between UserEdit and UserCreate controllers.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

trait UserEditCreateCommon
{
	private function _checkUserName($name)
	{
		return preg_match('/^[a-zA-Z0-9_\-.]+$/', $name);
	}

	// Takes current value of User::$permissions field. Returns array with permissions names (User::PERM_* constants names
	// without PERM_ prefix) as keys and true or false (which defines whether user has permission) as values.
	private function _prepareNamesArrayFromPermissionValue($currentPermissionsValue)
	{
		$possibleUserPermissions = array_filter(
			(new \ReflectionClass(WT\User::class))->getConstants(),
			function($constantName){ return (strpos($constantName, 'PERM_') === 0); },
			ARRAY_FILTER_USE_KEY
		);

		$namedPermissions = [];
		foreach ($possibleUserPermissions as $constantNameFull => $permissionValue) {
			$constantNamePart = str_replace('PERM_', null, $constantNameFull);
			$namedPermissions[$constantNamePart] = (bool)($currentPermissionsValue & $permissionValue);
		}
		return $namedPermissions;
	}

	// Takes array prepared by method above. Returns value for User::$permission field.
	private function _calculatePermissionValueFromNamesArray(array $currentNamedPermissions)
	{
		$permisionsValue = 0;
		foreach ($currentNamedPermissions as $constantNamePart => $permissionEnabled) {
			if ($permissionEnabled) {
				$permisionsValue = $permisionsValue | constant(WT\User::class . '::PERM_' . $constantNamePart);
			}
		}
		return $permisionsValue;
	}
}