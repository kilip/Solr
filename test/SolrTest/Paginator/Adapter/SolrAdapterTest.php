<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Paginator\Adapter;

use PHPUnit\Framework\TestCase;


use Solr\Bridge\ResultConverter;
use Solr\Filter\AbstractPaginationQuery;
use Solr\Paginator\Adapter\SolrAdapter;
use Solr\Facets;
use SolrDisMaxQuery;
use ArrayObject;

/**
 * Class SolrAdapterTest
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @author  Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author  Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since   0.26
 * @package SolrTest\Paginator\Adapter
 * @covers \Solr\Paginator\Adapter\SolrAdapter
 * @requires extension solr
 */
class SolrAdapterTest extends TestCase
{
    /**
     * Class Under test
     * @var SolrAdapter
     */
    protected $target;

    /**
     * SolrClient Mock
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $client;

    /**
     * AbstractPaginationQuery Mock
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filter;

    /**
     * Mock of SolrResponse
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * Mock of ResultConverter class
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $converter;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $facets;

    protected $responseArray = [
        'response' => [
            'numFound' => 3,
            'docs' => [
                ['index' => 1],
                ['index' => 2],
                ['index' => 3]
            ]
        ]
    ];

    protected function setUp(): void
    {
        $client = $this->getMockBuilder(\SolrClient::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $filter = $this->getMockBuilder(AbstractPaginationQuery::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $resultConverter = $this->getMockBuilder(ResultConverter::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $this->facets = $this->getMockBuilder(Facets::class)
            ->getMock();

        $this->target = new SolrAdapter($client, $filter, $resultConverter, $this->facets, array());
        $this->response = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['getArrayResponse', 'getResponse'])
            ->getMock()
        ;
        $this->client = $client;
        $this->filter = $filter;
        $this->converter = $resultConverter;

        $this->response
            ->method('getArrayResponse')
            ->willReturn($this->responseArray)
        ;
    }

    public function testGetItemsAndCount()
    {
        $response = new ArrayObject($this->responseArray);
        
        $this->response->method('getResponse')
            ->willReturn($response);
        
        $this->client
            ->expects($this->any())
            ->method('query')
            ->with($this->isInstanceOf(SolrDisMaxQuery::class))
            ->willReturn($this->response)
        ;

        $this->filter
            ->expects($this->any())
            ->method('filter')
            ->willReturn(new SolrDisMaxQuery())
        ;

        $this->converter
            ->expects($this->once())
            ->method('convert')
            ->with($this->filter, $response)
            ->willReturn([])
        ;

        $retVal = $this->target->getItems(0,5);
        $this->assertEquals([],$retVal);
        $this->assertEquals(3,$this->target->count());
    }

    /**
     * @expectedException \Solr\Exception\ServerException
     * @expectedExceptionMessage Failed to process query
     */
    public function testThrowException()
    {
        $this->client
            ->expects($this->once())
            ->method('query')
            ->with($this->isInstanceOf(SolrDisMaxQuery::class))
            ->willReturnOnConsecutiveCalls($this->throwException(new \Exception()))
        ;

        $this->filter
            ->expects($this->any())
            ->method('filter')
            ->willReturn(new SolrDisMaxQuery())
        ;

        $this->target->count();
    }
    
    public function testGetFacets()
    {
        $facetResult = new ArrayObject([
            'facetResult'
        ]);
        $response = new ArrayObject([
            'facet_counts' => $facetResult
        ], ArrayObject::ARRAY_AS_PROPS);
        
        $this->response->method('getResponse')
            ->willReturn($response);
        
        $this->client->expects($this->any())
            ->method('query')
            ->willReturn($this->response);
        
        $this->facets->expects($this->once())
            ->method('setFacetResult')
            ->with($this->identicalTo($facetResult))
            ->willReturnSelf();
        
        $this->assertSame($this->facets, $this->target->getFacets());
    }
}