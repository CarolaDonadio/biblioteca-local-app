<?php

use App\Filters\AdminAuthFilter;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Test\CIUnitTestCase;

final class AdminAuthFilterTest extends CIUnitTestCase
{
    public function testAdminFilterRequiresSession(): void
    {
        $filter = new AdminAuthFilter();
        $request = $this->createMock(RequestInterface::class);

        service('session')->destroy();

        $response = $filter->before($request);

        $this->assertStringEndsWith('/admin/login', (string) $response->getHeaderLine('Location'));
    }

    public function testAdminFilterExpiresInactiveSession(): void
    {
        $filter = new AdminAuthFilter();
        $request = $this->createMock(RequestInterface::class);
        service('session')->set([
            'admin_id' => 31001001,
            'admin_last_activity' => time() - 1801,
        ]);

        $response = $filter->before($request);

        $this->assertStringEndsWith('/admin/login', (string) $response->getHeaderLine('Location'));
    }
}