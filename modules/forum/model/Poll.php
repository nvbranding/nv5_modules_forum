<?php

if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}

function ModelPoll_getPollById( $id )
{
	global $db_slave;

	return $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_poll WHERE poll_id = ' . intval( $id ) )->fetch();
}

function getPollByContent( $contentType, $contentId )
{
	global $db_slave;

	return $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_poll WHERE content_type = ' . $db_slave->quote( $contentType ) . ' AND content_id = ' . intval( $contentId ) )->fetch();
}

function ModelPoll_getPollIdsInRange( $start, $limit )
{
	global $db_slave;

	$result = $db_slave->query(' SELECT poll_id FROM ' . NV_FORUM_GLOBALTABLE . '_poll WHERE poll_id > '. intval( $start ) .' ORDER BY poll_id LIMIT 0, '. $limit );
	$data = array();
	while( $poll_id = $result->fetch( 3 ) )
	{
		$data[] =  $poll_id;
		
	}
	$result->closeCursor();
	return $data;
}
 
function ModelPoll_getPollResponseById( $id )
{	
	global $db_slave;
	return $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_poll_response WHERE  poll_response_id = ' . intval( $id ) )->fetch();
}
 
function ModelPoll_getPollResponsesInPoll( $pollId )
{
	global $db_slave;
	
	$result =  $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_poll_response WHERE poll_id = ' . intval( $pollId ) )->fetch();
	$data = array();
	while( $rows = $result->fetch(  ) )
	{
		$data[$rows['poll_response_id']] = $rows;
		
	}
	$result->closeCursor();
	return $data;
 
}
 
function ModelPoll_getPollResponseCache( $pollId )
{
	$responses = ModelPoll_getPollResponsesInPoll( $pollId );
	$output = array();

	foreach( $responses as $response )
	{
		$output[$response['poll_response_id']] = array(
			'response' => $response['response'],
			'response_vote_count' => $response['response_vote_count'],
			'voters' => safeUnserialize( $response['voters'] ) );
	}

	return $output;
}

function ModelPoll_rebuildPollResponseCache( $pollId )
{
	global $db_slave;
	
	$cache = ModelPoll_getPollResponseCache( $pollId );
 	 
	$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_poll SET 
	responses=' . $db_slave->quote( serialize( $cache ) ). ' , 
	poll_id = ' . $db->quote( $pollId ) );

	return $cache;
}

function ModelPoll_preparePollResponsesFromCache( $responses )
{

	if( ! is_array( $responses ) )
	{
		$responses = safeUnserialize( $responses );
	}
	if( ! is_array( $responses ) )
	{
		return false;
	}

	foreach( $responses as &$response )
	{
		$response['response'] =  $response['response'];
		$response['hasVoted'] = isset( $response['voters'][$viewingUser['user_id']] );
	}

	return $responses;
}

function ModelPoll_preparePoll( array $poll, $canVote )
{
	if( ! is_array( $poll['responses'] ) )
	{
		$poll['responses'] = ModelPoll_preparePollResponsesFromCache( $poll['responses'], $viewingUser );
	}
	if( ! is_array( $poll['responses'] ) )
	{
		$poll['responses'] = ModelPoll_preparePollResponsesFromCache( ModelPoll_rebuildPollResponseCache( $poll['poll_id'] ), $viewingUser );
	}

	$poll['hasVoted'] = false;
	foreach( $poll['responses'] as $response )
	{
		if( ! empty( $response['hasVoted'] ) )
		{
			$poll['hasVoted'] = true;
			break;
		}
	}

	$poll['open'] = ( ! $poll['close_date'] || $poll['close_date'] > NV_CURRENTTIME );

	$poll['canViewResults'] = $poll['hasVoted'] || $poll['view_results_unvoted'] || ! $poll['open'];

	if( $canVote && $poll['open'] )
	{
		// base can vote permission and the poll is open...
		if( ! $poll['hasVoted'] || $poll['change_vote'] )
		{
			// ...can vote if they haven't voted or can change their vote
			$poll['canVote'] = true;
		}
		else
		{
			$poll['canVote'] = false;
		}
	}
	else
	{
		$poll['canVote'] = false;
	}

	$poll['question'] = $poll['question'];

	return $poll;
}

function ModelPoll_canVoteOnPoll( array $poll, &$errorLangKey = '' )
{
	if( $poll['close_date'] && $poll['close_date'] < NV_CURRENTTIME )
	{
		return false;
	}

	if( ! $viewingUser['user_id'] )
	{
		return false;
	}

	if( $poll['change_vote'] )
	{
		return true;
	}

	return ( ModelPoll_hasVotedOnPoll( $poll['poll_id'], $viewingUser['user_id'] ) ? false : true );
}

function ModelPoll_hasVotedOnPoll( $pollId, $userId )
{
	global $db_slave;
	
	$voted = $db_slave->query( 'SELECT poll_response_id FROM '. NV_FORUM_GLOBALTABLE .'_poll_vote WHERE poll_id = '. intval( $pollId ) .' AND user_id = '. intval( $userId  ) )->fetch();
 
	return ( $voted ? true : false );
}

function ModelPoll_voteOnPoll( $pollId, $votes, $userId = null, $voteDate = null )
{
	global $db, $global_userid, $nv_Request;
	if( ! is_array( $votes ) )
	{
		if( ! $votes )
		{
			return false;
		}
		$votes = array( $votes );
	}
	if( ! $votes )
	{
		return false;
	}

	if( $userId === null )
	{
		$userId = $global_userid;
	}
	if( ! $userId )
	{
		return false;
	}

	if( $voteDate === null )
	{
		$voteDate = NV_CURRENTTIME;
	}

	$responses = ModelPoll_getPollResponsesInPoll( $pollId );

 
	PDO::beginTransaction( $db );

	$db->query( 'SELECT poll_id FROM '. NV_FORUM_GLOBALTABLE .'_poll WHERE poll_id = '. intval( $pollId ) .' FOR UPDATE');

	$previousVotes = $db->query( 'DELETE FROM '. NV_FORUM_GLOBALTABLE .'_poll_vote WHERE poll_id = ' . $db->quote( $pollId ) . ' AND userid = ' . $db->quote( $userId ) );
	
	$newVoter = ( $previousVotes->rowCount() == 0 );

	// with a new voter, we take some shortcuts and just rebuild what they touched.
	// when someone changes their vote lets be sure and rebuild everything.
	// the select for update above should make this be consistent.

	foreach( $votes as $voteResponseId )
	{
		if( ! isset( $responses[$voteResponseId] ) )
		{
			continue;
		}

		$res = $db->query( '
				INSERT IGNORE INTO '. NV_FORUM_GLOBALTABLE .'_poll_vote
					(user_id, poll_response_id, poll_id, vote_date)
				VALUES
					('. intval( $userId ) .', '. intval( $voteResponseId ) .', '. intval( $pollId ) .', '. intval( $voteDate ) .')');
		if( $newVoter && $res->rowCount() )
		{
			$voterCache = ModelPoll_getPollResponseVoterCache( $voteResponseId );
			$db->query( '
					UPDATE '. NV_FORUM_GLOBALTABLE .'_poll_response SET
						response_vote_count = response_vote_count + 1,
						voters = '. $db->quote( serialize( $voterCache ) ) .'
					WHERE poll_response_id = '. intval( $voteResponseId ) );
				 
		}
	}

	if( $newVoter )
	{
		$voter_count = $nv_Request->get_int( 'voter_count', 'post,get', 0 );
		$voter_count = $voter_count + 1;
		$db->query( '
			UPDATE '. NV_FORUM_GLOBALTABLE .'_poll SET
				voter_count = '.intval( $voter_count ).' 
			WHERE poll_id = '. intval( $pollId ) );
		  
	}
	else
	{
		ModelPoll_rebuildPollData( $pollId );
	}

	PDO::commit( $db );

	return true;
}

function ModelPoll_getPollResponseVoterCache( $pollResponseId )
{
	global $db_slave;
 
	$result =  $db_slave->query( 'SELECT poll_vote.userid, user.username FROM '. NV_FORUM_GLOBALTABLE .'_poll_vote AS poll_vote LEFT JOIN '. NV_USERS_GLOBALTABLE .' AS user ON (poll_vote.userid = user.userid) WHERE poll_vote.poll_response_id = '. intval( $pollResponseId )  )->fetch();
	$data = array();
	while( $rows = $result->fetch(  ) )
	{
		$data[$rows['userid']] = $rows;
		
	}
	$result->closeCursor();
	return $data;
 
}

function ModelPoll_getPollVoterCount( $pollId )
{
	global $db_slave;
	return $db_slave->query( 'SELECT COUNT(DISTINCT user_id) FROM '. NV_FORUM_GLOBALTABLE .'_poll_vote WHERE poll_id = '. intval(  $pollId ) )->fetchColumn();
}

function ModelPoll_resetPoll( $pollId )
{
	global $db;

	PDO::beginTransaction( $db );

	$db->query( 'DELETE FROM '. NV_FORUM_GLOBALTABLE .'_poll_vote WHERE poll_id = ' . $db->quote( $pollId ) );
	
	ModelPoll_rebuildPollData( $pollId );

	PDO::commit( $db );
}

function ModelPoll_rebuildPollData( $pollId )
{
	global $db;

	$votes = array();
	$voters = array();
	$result = $db->query( '
			SELECT poll_vote.poll_response_id, poll_vote.userid, user.username
			FROM '. NV_FORUM_GLOBALTABLE .'_poll_vote AS poll_vote
			LEFT JOIN '. NV_USERS_GLOBALTABLE .' AS user ON (poll_vote.userid = user.userid)
			WHERE poll_vote.poll_id = '. intval( $pollId ) );
	while( $vote = $result->fetch() )
	{
		$votes[$vote['poll_response_id']][$vote['user_id']] = array( 'user_id' => $vote['user_id'], 'username' => $vote['username'] );
		$voters[$vote['userid']] = true;
	}
	$result->closeCursor();

	$responses = ModelPoll_getPollResponsesInPoll( $pollId );

	PDO::beginTransaction( $db );

	foreach( $responses as $responseId => $response )
	{
		if( ! isset( $votes[$responseId] ) )
		{
			$db->query( 'UPDATE '. NV_FORUM_GLOBALTABLE .'_poll_response SET response_vote_count=0, voters= \'\' WHERE poll_response_id = ' . $db->quote( $responseId ) );
		}
		else
		{
			$db->query( 'UPDATE '. NV_FORUM_GLOBALTABLE .'_poll_response SET response_vote_count = ' . count( $votes[$responseId] ) . ', voters = ' . $db->quote( serialize( $votes[$responseId] ) ) . ' WHERE poll_response_id = ' . $db->quote( $responseId ) );
		}
	}
	
	$db->query( 'UPDATE '. NV_FORUM_GLOBALTABLE .'_poll SET voter_count = ' . count( $voters ) . ', responses = ' . $db->quote( serialize( ModelPoll_getPollResponseCache( $pollId ) ) ) . ' WHERE poll_id = ' . $db->quote( $pollId ) );
	
	 
	PDO::commit( $db );
}

function ModelPoll_setupNewPollFromForm( XenForo_Input $input )
{
	// $data = array(
		// 'question' => XenForo_Input::STRING,
		// 'responses' => array( XenForo_Input::STRING, 'array' => true ),
		// 'max_votes_type' => XenForo_Input::STRING,
		// 'max_votes_value' => XenForo_Input::UINT,
		// 'public_votes' => XenForo_Input::BOOLEAN,
		// 'change_vote' => XenForo_Input::BOOLEAN,
		// 'view_results_unvoted' => XenForo_Input::BOOLEAN,
		// 'close' => XenForo_Input::UINT,
		// 'close_length' => XenForo_Input::UNUM,
		// 'close_units' => XenForo_Input::STRING ) );

	// $pollWriter = XenForo_DataWriter::create( 'XenForo_DataWriter_Poll' );
	// $pollWriter->bulkSet( array(
		// 'question' => $pollInput['question'],
		// 'public_votes' => $pollInput['public_votes'],
		// 'change_vote' => $pollInput['change_vote'],
		// 'view_results_unvoted' => $pollInput['view_results_unvoted'],
		// ) );

	// switch( $pollInput['max_votes_type'] )
	// {
		// case 'single':
			// $pollWriter->set( 'max_votes', 1 );
			// break;

		// case 'unlimited':
			// $pollWriter->set( 'max_votes', 0 );
			// break;

		// default:
			// $pollWriter->set( 'max_votes', $pollInput['max_votes_value'] );
	// }

	// if( $pollInput['close'] )
	// {
		// if( ! $pollInput['close_length'] )
		// {
			// $pollWriter->error( new XenForo_Phrase( 'please_enter_valid_length_of_time' ) );
		// }
		// else
		// {
			// $pollWriter->set( 'close_date', $pollWriter->preVerifyCloseDate( strtotime( '+' . $pollInput['close_length'] . ' ' . $pollInput['close_units'] ) ) );
		// }
	// }

	// $pollWriter->addResponses( $pollInput['responses'] );
	$pollWriter = '';
	return $pollWriter;
}
