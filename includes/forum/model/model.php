<?php

function getConditionsForClause( array $sqlConditions )
{
	if( $sqlConditions )
	{
		return '(' . implode( ') AND (', $sqlConditions ) . ')';
	}
	else
	{
		return '1=1';
	}
}
function assertValidCutOffOperator( $operator, $allowBetween = false )
{
	switch( $operator )
	{
		case '<':
		case '<=':
		case '=':
		case '>':
		case '>=':
			break;

		case '>=<':
			if( $allowBetween )
			{
				return;
			}
			// break missing intentionally

		default:
			trigger_error( 'Invalid cut off operator.' );
	}
}
function getCutOffCondition( $column, $condition )
{
	global $db;
	list( $operator, $cutOff ) = $condition;

	assertValidCutOffOperator( $operator, true );

	if( $operator === '>=<' )
	{
		list( , $cutOffLower, $cutOffHigher ) = $condition;

		return "($column >= " . $db->quote( $cutOffLower ) . " AND $column <= " . $db->quote( $cutOffHigher ) . ')';
	}
	else
	{
		return "$column $operator " . $db->quote( $cutOff );
	}
}

function prepareStateLimitFromConditions( array $fetchOptions, $table = '', $stateField = 'message_state', $userField = 'userid' )
{
	$fetchOptions = array_merge( array( 'deleted' => false, 'moderated' => false ), $fetchOptions );

	$stateRef = ( $table ? "$table.$stateField" : $stateField );
	$userRef = ( $table ? "$table.$userField" : $userField );
 
	$states = array( "'visible'" );
	$moderatedLimit = '';

	if( $fetchOptions['deleted'] )
	{
		$states[] = "'deleted'";
	}

	if( $fetchOptions['moderated'] )
	{
		if( $fetchOptions['moderated'] === true )
		{
			$states[] = "'moderated'";
		}
		else
		{
			$moderatedLimit = " OR ($stateRef = 'moderated' AND $userRef = " . intval( $fetchOptions['moderated'] ) . ')';
		}
	}

	return "$stateRef IN (" . implode( ',', $states ) . ")$moderatedLimit";
}

function prepareLimitFetchOptions(array $fetchOptions)
	{
		$limitOptions = array('limit' => 0, 'offset' => 0);
		if (isset($fetchOptions['limit']))
		{
			$limitOptions['limit'] = intval($fetchOptions['limit']);
		}
		if (isset($fetchOptions['offset']))
		{
			$limitOptions['offset'] = intval($fetchOptions['offset']);
		}

		if (isset($fetchOptions['perPage']) && $fetchOptions['perPage'] > 0)
		{
			$limitOptions['limit'] = intval($fetchOptions['perPage']);
		}

		if (isset($fetchOptions['page']))
		{
			$page = intval($fetchOptions['page']);
			if ($page < 1)
			{
				$page = 1;
			}

			$limitOptions['offset'] = intval(($page - 1) * $limitOptions['limit']);

			if (!empty($fetchOptions['pageExtra']) && $limitOptions['limit'])
			{
				$limitOptions['limit'] += max(0, intval($fetchOptions['pageExtra']));
			}
		}

		return $limitOptions;
	} 