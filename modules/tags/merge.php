<?php

$http = eZHTTPTool::instance();

$tagID = $Params['TagID'];

if ( is_numeric($tagID) && $tagID > 0 )
{
	$tag = eZTagsObject::fetch($tagID);
	if(!$tag)
	{
		return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
	}

	if($tag->MainTagID != 0)
	{
		return $Module->redirectToView( 'merge', array( $tag->MainTagID ) );
	}

	if($http->hasPostVariable('DiscardButton'))
	{
		return $Module->redirectToView( 'id', array( $tagID ) );
	}
	else if($http->hasPostVariable('SaveButton'))
	{
		if($http->hasPostVariable('MainTagID') && is_numeric($http->postVariable('MainTagID'))
			&& (int) $http->postVariable('MainTagID') > 0)
		{
			$currentTime = time();
			$mainTag = eZTagsObject::fetch((int) $http->postVariable('MainTagID'));
			$oldParentTag = $tag->getParent();

			$db = eZDB::instance();
			$db->begin();

			if($oldParentTag)
			{
				$oldParentTag->Modified = $currentTime;
				$oldParentTag->store();
			}

			$children = $tag->getChildren();
			foreach($children as $child)
			{
				$childSynonyms = $child->getSynonyms();

				foreach($childSynonyms as $childSynonym)
				{
					$childSynonym->ParentID = $mainTag->ID;
					$childSynonym->Modified = $currentTime;
					$childSynonym->store();
				}

				$child->ParentID = $mainTag->ID;
				$child->Modified = $currentTime;
				$child->store();
			}

			$synonyms = $tag->getSynonyms();
			foreach($synonyms as $synonym)
			{
				foreach($synonym->TagAttributeLinks as $tagAttributeLink)
				{
					if(!$mainTag->isRelatedToObject($tagAttributeLink->ObjectAttributeID, $tagAttributeLink->ObjectID))
					{
						$tagAttributeLink->KeywordID = $mainTag->ID;
						$tagAttributeLink->store();
					}
					else
					{
						$tagAttributeLink->remove();
					}
				}

				$synonym->remove();
			}

			foreach($tag->TagAttributeLinks as $tagAttributeLink)
			{
				if(!$mainTag->isRelatedToObject($tagAttributeLink->ObjectAttributeID, $tagAttributeLink->ObjectID))
				{
					$tagAttributeLink->KeywordID = $mainTag->ID;
					$tagAttributeLink->store();
				}
				else
				{
					$tagAttributeLink->remove();
				}
			}

			$mainTag->Modified = $currentTime;
			$mainTag->store();

			$tag->remove();

			$db->commit();

			return $Module->redirectToView( 'id', array( $mainTag->ID ) );
		}
		else
		{
			return $Module->redirectToView( 'merge', array( $tagID ) );
		}
	}
	else
	{
		$tpl = eZTemplate::factory();

		$tpl->setVariable('tag', $tag);

		$Result = array();
		$Result['content'] = $tpl->fetch( 'design:tags/merge.tpl' );
		$Result['ui_context'] = 'edit';
		$Result['path'] = array();

		$tempTag = $tag;
		while($tempTag->hasParent())
		{
			$tempTag = $tempTag->getParent();
			$Result['path'][] = array(  'tag_id' => $tempTag->ID,
			                            'text' => $tempTag->Keyword,
		                                'url' => false );
		}

		$Result['path'] = array_reverse($Result['path']);
		$Result['path'][] = array(  'tag_id' => $tag->ID,
		                            'text' => $tag->Keyword,
		                            'url' => false );

		$contentInfoArray = array();
		$contentInfoArray['persistent_variable'] = false;
		if ( $tpl->variable( 'persistent_variable' ) !== false )
			$contentInfoArray['persistent_variable'] = $tpl->variable( 'persistent_variable' );

		$Result['content_info'] = $contentInfoArray;

		return $Result;
	}
}
else
{
	return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

?>
