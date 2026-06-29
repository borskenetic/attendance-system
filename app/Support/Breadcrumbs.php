<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Breadcrumbs
{
    /**
     * Sidebar-aligned navigation tree for breadcrumb resolution.
     *
     * @var array<int, array<string, mixed>>
     */
    private const NAVIGATION = [
        [
            'label' => 'Home',
            'route' => 'home',
        ],
        [
            'label' => 'Attendance',
            'children' => [
                [
                    'label' => 'Gate Terminal',
                    'routes' => ['attendance.scan'],
                ],
                [
                    'label' => 'Attendance Logs',
                    'routes' => [
                        'attendance_logs.index',
                        'attendance_logs.export.*',
                        'attendance_logs.reports.*',
                    ],
                    'url' => 'attendance_logs.index',
                ],
                [
                    'label' => 'Manage Video',
                    'routes' => ['attendance.changeVideo', 'attendance.uploadVideo'],
                    'url' => 'attendance.changeVideo',
                ],
                [
                    'label' => 'Section Picker',
                    'routes' => ['attendance.section.settings', 'attendance.section.settings.update'],
                    'url' => 'attendance.section.settings',
                ],
                [
                    'label' => 'Logout Feedback',
                    'routes' => ['attendance.feedback.settings', 'attendance.feedback.settings.update'],
                    'url' => 'attendance.feedback.settings',
                ],
            ],
        ],
        [
            'label' => 'Data',
            'children' => [
                [
                    'label' => 'Students',
                    'routes' => [
                        'students.index',
                        'students.report',
                        'students.create',
                        'students.edit',
                        'students.store',
                        'students.update',
                        'students.import*',
                        'students.bulk.*',
                        'students.export',
                        'students.pending',
                        'students.approve',
                        'students.reject',
                        'pending.index',
                        'idcard.*',
                    ],
                    'url' => 'students.index',
                ],
                [
                    'label' => 'Employees',
                    'routes' => [
                        'employees.index',
                        'employees.create',
                        'employees.edit',
                        'employees.store',
                        'employees.update',
                        'employees.import*',
                        'employees.bulk.*',
                        'employees.export',
                        'employees.id.*',
                        'employees.idcard.*',
                        'pending.employees',
                        'employees.approve',
                        'employees.reject',
                    ],
                    'url' => 'employees.index',
                ],
            ],
        ],
        [
            'label' => 'Communication',
            'children' => [
                [
                    'label' => 'Feedback',
                    'routes' => ['feedback.index'],
                    'url' => 'feedback.index',
                ],
                [
                    'label' => 'SMS Blast',
                    'routes' => ['sms.page', 'sms.send', 'sms.count'],
                    'url' => 'sms.page',
                ],
                [
                    'label' => 'Scanner Message',
                    'routes' => ['sms.scanMessage', 'sms.scanMessage.update'],
                    'url' => 'sms.scanMessage',
                ],
            ],
        ],
        [
            'label' => 'Admin',
            'children' => [
                [
                    'label' => 'School Setup',
                    'routes' => ['prospectus.*'],
                    'url' => 'prospectus.index',
                ],
                [
                    'label' => 'Create Account',
                    'routes' => ['users.create', 'users.store'],
                    'url' => 'users.create',
                ],
                [
                    'label' => 'View Accounts',
                    'routes' => ['users.index', 'users.edit', 'users.update', 'users.destroy'],
                    'url' => 'users.index',
                ],
                [
                    'label' => 'Files',
                    'routes' => ['files.*'],
                    'url' => 'files.index',
                ],
            ],
        ],
    ];

    /**
     * @var array<string, string>
     */
    private const ROUTE_LABELS = [
        'home' => 'Home',
        'login' => 'Login',
        'patron.register' => 'Register',
        'students.create' => 'Register Student',
        'students.edit' => 'Edit Student',
        'students.report' => 'Student Report',
        'students.pending' => 'Pending Students',
        'pending.index' => 'Pending Approvals',
        'pending.employees' => 'Pending Employees',
        'employees.create' => 'Add Employee',
        'employees.edit' => 'Edit Employee',
        'users.create' => 'Create Account',
        'users.edit' => 'Edit Account',
        'attendance_logs.reports.hub' => 'Reports Hub',
        'attendance_logs.reports.dashboard' => 'Reports Dashboard',
        'attendance_logs.reports.export' => 'Export Reports',
        'attendance_logs.export.excel' => 'Export Excel',
        'attendance_logs.export.pdf' => 'Export PDF',
        'prospectus.storeProgram' => 'Add Program',
        'prospectus.storeCourse' => 'Add Course',
        'prospectus.updateCourse' => 'Edit Course',
        'prospectus.updateProgram' => 'Edit Program',
    ];

    /**
     * @param  array<int, array{label: string, url?: string|null, current?: bool}>|null  $override
     * @return array<int, array{label: string, url: string|null, current: bool}>
     */
    public static function resolve(?string $routeName = null, ?array $override = null): array
    {
        if (is_array($override) && $override !== []) {
            return self::normalize($override);
        }

        $routeName = $routeName ?? Route::currentRouteName();

        if (! $routeName) {
            return [];
        }

        if ($routeName === 'home') {
            return [self::item('Home', null, true)];
        }

        if ($routeName === 'patron.register') {
            return [
                self::item('Home', 'home'),
                self::item('Register', null, true),
            ];
        }

        if ($routeName === 'login') {
            return [
                self::item('Home', 'home'),
                self::item('Login', null, true),
            ];
        }

        $trail = self::findTrail(self::NAVIGATION, $routeName);

        if ($trail === null) {
            return [
                self::item('Home', 'home'),
                self::item(self::labelForRoute($routeName), null, true),
            ];
        }

        $items = [self::item('Home', 'home')];
        $lastIndex = count($trail) - 1;

        foreach ($trail as $index => $node) {
            $isLast = $index === $lastIndex;
            $label = $isLast ? self::labelForRoute($routeName, $node['label']) : $node['label'];
            $url = null;

            if (! $isLast) {
                $url = self::urlForNode($node);
            } elseif (isset($node['url']) && is_string($node['url'])) {
                $url = self::safeRoute($node['url']);
            }

            $items[] = self::item($label, $isLast ? null : $url, $isLast);
        }

        return $items;
    }

    /**
     * @param  array<int, array{label: string, url?: string|null, current?: bool}>  $items
     * @return array<int, array{label: string, url: string|null, current: bool}>
     */
    private static function normalize(array $items): array
    {
        return array_values(array_map(function (array $item, int $index) use ($items) {
            $isLast = $item['current'] ?? ($index === count($items) - 1);

            return self::item(
                $item['label'],
                $isLast ? null : ($item['url'] ?? null),
                $isLast,
            );
        }, $items, array_keys($items)));
    }

    /**
     * @return array{label: string, url: string|null, current: bool}
     */
    private static function item(string $label, ?string $routeOrUrl = null, bool $current = false): array
    {
        $url = null;

        if ($routeOrUrl && ! $current) {
            $url = str_starts_with($routeOrUrl, 'http') || str_starts_with($routeOrUrl, '/')
                ? $routeOrUrl
                : self::safeRoute($routeOrUrl);
        }

        return [
            'label' => $label,
            'url' => $url,
            'current' => $current,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $nodes
     * @param  array<int, array<string, mixed>>  $parents
     * @return array<int, array<string, mixed>>|null
     */
    private static function findTrail(array $nodes, string $routeName, array $parents = []): ?array
    {
        foreach ($nodes as $node) {
            if (isset($node['route']) && $node['route'] === $routeName) {
                return [...$parents, $node];
            }

            if (self::nodeMatchesRoute($node, $routeName)) {
                return [...$parents, $node];
            }

            if (! empty($node['children'])) {
                $trail = self::findTrail($node['children'], $routeName, [...$parents, $node]);

                if ($trail !== null) {
                    return $trail;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    private static function nodeMatchesRoute(array $node, string $routeName): bool
    {
        if (! empty($node['routes'])) {
            foreach ($node['routes'] as $pattern) {
                if (Str::is($pattern, $routeName)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    private static function urlForNode(array $node): ?string
    {
        if (! empty($node['url'])) {
            return self::safeRoute($node['url']);
        }

        if (! empty($node['route'])) {
            return self::safeRoute($node['route']);
        }

        if (! empty($node['children'])) {
            foreach ($node['children'] as $child) {
                if (! empty($child['url'])) {
                    return self::safeRoute($child['url']);
                }

                if (! empty($child['route'])) {
                    return self::safeRoute($child['route']);
                }
            }
        }

        return null;
    }

    private static function labelForRoute(string $routeName, ?string $fallback = null): string
    {
        if (isset(self::ROUTE_LABELS[$routeName])) {
            return self::ROUTE_LABELS[$routeName];
        }

        if ($fallback) {
            return $fallback;
        }

        return Str::headline(str_replace('.', ' ', Str::afterLast($routeName, '.')));
    }

    private static function safeRoute(string $name): ?string
    {
        if (! Route::has($name)) {
            return null;
        }

        return route($name);
    }
}
