<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Synchronization\Business;

use Codeception\Test\Unit;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Synchronization\Business\SynchronizationBusinessFactory;
use Spryker\Zed\Synchronization\Business\SynchronizationFacade;
use Spryker\Zed\Synchronization\Dependency\Client\SynchronizationToSearchInterface;
use Spryker\Zed\Synchronization\Dependency\Client\SynchronizationToStorageInterface;
use Spryker\Zed\Synchronization\Dependency\Service\SynchronizationToUtilEncodingInterface;
use Spryker\Zed\Synchronization\SynchronizationDependencyProvider;

/**
 * @group Functional
 * @group Spryker
 * @group Zed
 * @group Synchronization
 * @group Business
 * @group SynchronizationFacadeTest
 */
class SynchronizationFacadeTest extends Unit
{

    /**
     * @var \Spryker\Zed\Synchronization\Business\SynchronizationFacadeInterface
     */
    protected $synchronizationFacade;

    /**
     * @return void
     */
    public function testSynchronizationWritesDataToStorage()
    {
        $container = new Container();
        $container[SynchronizationDependencyProvider::CLIENT_STORAGE] = function (Container $container) {
            $storageMock = $this->createStorageClientBridge();
            $storageMock->expects($this->once())->method('set')->will(
                $this->returnCallback(
                    function ($key, $value) {
                        $this->assertEquals($key, 'testKey');
                        $this->assertEquals($value, ['data' => 'testValue']);
                    }
                )
            );

            return $storageMock;
        };

        $container[SynchronizationDependencyProvider::SERVICE_UTIL_ENCODING] = function (Container $container) {
            $utilEncodingMock = $this->createUtilEncodingServiceBridge();
            $utilEncodingMock->expects($this->once())->method('encodeJson')->willReturnArgument(0);

            return $utilEncodingMock;
        };

        $this->prepareFacade($container);
        $this->synchronizationFacade->storageWrite([
            'key' => 'testKey',
            'value' => ['data' => 'testValue'],
        ], 'test');
    }

    /**
     * @return void
     */
    public function testSynchronizationDeletesDataToStorage()
    {
        $container = new Container();
        $container[SynchronizationDependencyProvider::CLIENT_STORAGE] = function (Container $container) {
            $storageMock = $this->createStorageClientBridge();
            $storageMock->expects($this->once())->method('delete')->will(
                $this->returnCallback(
                    function ($key) {
                        $this->assertEquals($key, 'testKey');
                    }
                )
            );

            return $storageMock;
        };

        $container[SynchronizationDependencyProvider::SERVICE_UTIL_ENCODING] = function (Container $container) {
            return $this->createUtilEncodingServiceBridge();
        };

        $this->prepareFacade($container);
        $this->synchronizationFacade->storageDelete([
            'key' => 'testKey',
            'value' => ['data' => 'testValue'],
        ], 'test');
    }

    /**
     * @return void
     */
    public function testSynchronizationWritesDataToSearch()
    {
        $container = new Container();
        $container[SynchronizationDependencyProvider::CLIENT_SEARCH] = function (Container $container) {
            $storageMock = $this->createSearchClientBridge();
            $storageMock->expects($this->once())->method('write')->will(
                $this->returnCallback(
                    function ($data) {
                        $this->assertEquals(key($data), 'testKey');
                        $this->assertEquals(current($data), ['data' => 'testValue']);
                    }
                )
            );

            return $storageMock;
        };

        $container[SynchronizationDependencyProvider::SERVICE_UTIL_ENCODING] = function (Container $container) {
            $utilEncodingMock = $this->createUtilEncodingServiceBridge();

            return $utilEncodingMock;
        };

        $this->prepareFacade($container);
        $this->synchronizationFacade->searchWrite([
            'key' => 'testKey',
            'value' => ['data' => 'testValue'],
        ], 'test');
    }

    /**
     * @return void
     */
    public function testSynchronizationDeleteDataToSearch()
    {
        $container = new Container();
        $container[SynchronizationDependencyProvider::CLIENT_SEARCH] = function (Container $container) {
            $storageMock = $this->createSearchClientBridge();
            $storageMock->expects($this->once())->method('delete')->will(
                $this->returnCallback(
                    function ($data) {
                        $this->assertEquals(key($data), 'testKey');
                    }
                )
            );

            return $storageMock;
        };

        $container[SynchronizationDependencyProvider::SERVICE_UTIL_ENCODING] = function (Container $container) {
            $utilEncodingMock = $this->createUtilEncodingServiceBridge();

            return $utilEncodingMock;
        };

        $this->prepareFacade($container);
        $this->synchronizationFacade->searchDelete([
            'key' => 'testKey',
            'value' => ['data' => 'testValue'],
        ], 'test');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createStorageClientBridge()
    {
        return $this->getMockBuilder(SynchronizationToStorageInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'set',
                'get',
                'delete',
            ])
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createSearchClientBridge()
    {
        return $this->getMockBuilder(SynchronizationToSearchInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'write',
                'read',
                'delete',
            ])
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createUtilEncodingServiceBridge()
    {
        return $this->getMockBuilder(SynchronizationToUtilEncodingInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'encodeJson',
                'decodeJson',
            ])
            ->getMock();
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return void
     */
    protected function prepareFacade($container)
    {
        $synchronizationBusinessFactory = new SynchronizationBusinessFactory();
        $synchronizationBusinessFactory->setContainer($container);

        $this->synchronizationFacade = new SynchronizationFacade();
        $this->synchronizationFacade->setFactory($synchronizationBusinessFactory);
    }

}
