<?php

/**
 * eZTagsFunctionCollection class implements fetch functions for eztags
 *
 */
class eZTagsFunctionCollection
{
    /**
     * Fetches eZTagsObject object for the provided tag ID
     *
     * @static
     * @param integer $tag_id
     * @return array
     */
    static public function fetchTag( $tag_id )
    {
        $result = eZTagsObject::fetch( $tag_id );

        if( $result instanceof eZTagsObject )
            return array( 'result' => $result );
        else
            return array( 'result' => false );
    }

    /**
     * Fetches all tags named with provided keyword
     *
     * @static
     * @param string $keyword
     * @return array
     */
    static public function fetchTagsByKeyword( $keyword )
    {
        $result = eZTagsObject::fetchByKeyword( $keyword );

        if( is_array( $result ) && !empty( $result ) )
            return array( 'result' => $result );
        else
            return array( 'result' => false );
    }

    /**
     * Fetches subtree of tags by specified parameters
     *
     * @static
     * @param integer $parentTagID
     * @param array $sortBy
     * @param integer $offset
     * @param integer $limit
     * @param integer $depth
     * @param string $depthOperator
     * @param bool $includeSynonyms
     * @return array
     */
    static public function fetchTagTree( $parentTagID, $sortBy, $offset, $limit, $depth, $depthOperator, $includeSynonyms )
    {
        if ( !is_numeric( $parentTagID ) || (int) $parentTagID < 0 )
            return array( 'result' => false );

        $params = array( 'SortBy' => $sortBy,
                         'Offset' => $offset,
                         'Limit'  => $limit,
                         'IncludeSynonyms' => $includeSynonyms );

        if ( $depth !== false )
        {
            $params['Depth'] = $depth;
            $params['DepthOperator'] = $depthOperator;
        }

        $tags = eZTagsObject::subTreeByTagID( $params, $parentTagID );

        return array( 'result' => $tags );
    }

    /**
     * Fetches subtree tag count by specified parameters
     *
     * @static
     * @param integer $parentTagID
     * @param integer $depth
     * @param string $depthOperator
     * @param bool $includeSynonyms
     * @return integer
     */
    static public function fetchTagTreeCount( $parentTagID, $depth, $depthOperator, $includeSynonyms )
    {
        if ( !is_numeric( $parentTagID ) || (int) $parentTagID < 0 )
            return array( 'result' => 0 );

        $params = array( 'IncludeSynonyms' => $includeSynonyms );

        if ( $depth !== false )
        {
            $params['Depth'] = $depth;
            $params['DepthOperator'] = $depthOperator;
        }

        $tagsCount = eZTagsObject::subTreeCountByTagID( $params, $parentTagID );

        return array( 'result' => $tagsCount );
    }

    /**
     * Fetches latest modified tags by specified parameters
     *
     * @static
     * @param integer $parentTagID
     * @param integer $limit
     * @return array
     */
    static public function fetchLatestTags( $parentTagID = false, $limit = 0 )
    {
        $filterArray = array( 'main_tag_id' => 0 );

        if ( $parentTagID !== false )
            $filterArray['parent_id'] = (int) $parentTagID;

        $result = eZPersistentObject::fetchObjectList( eZTagsObject::definition(), null,
                                                       $filterArray,
                                                       array( 'modified' => 'desc' ),
                                                       array( 'offset' => 0, 'limit' => $limit ) );

        if ( is_array( $result ) && !empty( $result ) )
            return array( 'result' => $result );
        else
            return array( 'result' => false );
    }

    private function lookupIcon( $tag )
    {
        $ini = eZINI::instance( 'eztags.ini' );
        $iconMap = $ini->variable( 'Icons', 'IconMap' );
        $returnValue = $ini->variable( 'Icons', 'Default' );

        if ( array_key_exists( $tag->attribute( 'id' ), $iconMap ) && !empty( $iconMap[$tag->attribute( 'id' )] ) )
        {
            $returnValue = $iconMap[$tag->attribute( 'id' )];
        }
        else
        {
            $tempTag = $tag;
            while ( $tempTag->attribute( 'parent_id' ) > 0 )
            {
                $tempTag = $tempTag->getParent();
                if ( array_key_exists( $tempTag->attribute( 'id' ), $iconMap ) && !empty( $iconMap[$tempTag->attribute( 'id' )] ) )
                {
                    $returnValue = $iconMap[$tempTag->attribute( 'id' )];
                    break;
                }
            }
        }
        
        return eZURLOperator::eZImage( eZTemplate::factory(), 'tag_icons/small/' . $returnValue, '', true );
    }
    
    /**
     * Fetches children for the given tag
     *
     * @param int $id
     * @return array
     */
    private function getChildrenForTag( $id )
    {
        $params = array( 'Depth' => 1 );
        $children = array();
        $tags = eZTagsObject::subTreeByTagID( $params, $id );

        if( count( $tags ) > 0 )
        {
            foreach( $tags as $tag )
            {
                $icon = $this->lookupIcon( $tag );
                $uri = 'tags/id/' . $tag->attribute( 'id' );
                eZURI::transformURI( $uri, false );
                $children[] = array( 
                    'title' => $tag->attribute( 'keyword' ),
                    'isFolder' => eZTagsObject::subTreeCountByTagID( $params, $tag->attribute( 'id' ) ) ? true : false,
                    'href' => $uri,
                    'icon' => $this->lookupIcon( $tag ),
                    'children' => self::getChildrenForTag( $tag->attribute( 'id' ) )
                );
            }
        }

        return $children;
    }

    /**
     * Fetches tags to build the treemenu
     *
     * @return json object
     */
    public function fetchTagsTreeMenu()
    {
        $params = array( 'Depth' => 1 );
        $tags = eZTagsObject::subTreeByTagID( $params, 0 );
        $result = array();
        $params = array();
        $result = array( 
            'title' => 'Top Level',
            'isFolder' => eZTagsObject::subTreeCountByTagID( $params, 0 ) ? true : false,
            'children' => $this->getChildrenForTag( 0 ),   
        );
        return array( 'result' => json_encode( $result ) );        
    }
     
}

?>
