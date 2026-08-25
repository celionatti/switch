<?php

declare(strict_types=1);

namespace Switch\View\Tests;

use PHPUnit\Framework\TestCase;
use Switch\View\Compiler\TemplateCompiler;

class CompilerTest extends TestCase
{
    private TemplateCompiler $compiler;

    protected function setUp(): void
    {
        $this->compiler = new TemplateCompiler();
    }

    public function testCompileInterpolation(): void
    {
        $template = '<h1>{{ $title }}</h1><div>{!! $rawHtml !!}</div>';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("<?= htmlspecialchars((string) (\$title)", $compiled);
        $this->assertStringContainsString("<?= (string) (\$rawHtml); ?>", $compiled);
    }

    public function testCompileCsrfTagAndDirectiveParity(): void
    {
        $template = '<form><csrf />@csrf<s-csrf /><csrf></csrf></form>';
        $compiled = $this->compiler->compile($template);

        $expected = '<?= \Switch\View\Security\SecurityHelper::csrfField(); ?>';
        $this->assertEquals(4, substr_count($compiled, $expected));
    }

    public function testCompileMethodSpoofingTagAndDirectiveParity(): void
    {
        $template = '<form><method value="PUT" />@method(\'DELETE\')<s-method value="PATCH" /></form>';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("SecurityHelper::methodField('PUT')", $compiled);
        $this->assertStringContainsString("SecurityHelper::methodField('DELETE')", $compiled);
        $this->assertStringContainsString("SecurityHelper::methodField('PATCH')", $compiled);
    }

    public function testCompileHoneypotTagAndDirectiveParity(): void
    {
        $template = '<honeypot />@honeypot<honeypot name="alt_hp" time="alt_time" />';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("SecurityHelper::honeypot('my_name_hp', 'my_time_hp')", $compiled);
        $this->assertStringContainsString("SecurityHelper::honeypot('alt_hp', 'alt_time')", $compiled);
    }

    public function testCompileNonceTagAndDirectiveParity(): void
    {
        $template = '<script <nonce />>@nonce</script>';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString('nonce="<?= \Switch\View\Security\SecurityHelper::getCspNonce(); ?>"', $compiled);
    }

    public function testCompileLiveScriptsAndNotificationStreamParity(): void
    {
        $template = '<live-scripts /><liveScripts />@liveScripts<notification-stream />@notificationStream<notifications />';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("live_scripts()", $compiled);
        $this->assertStringContainsString("notification_stream()", $compiled);
    }

    public function testCompileHeadMetaTagAndDirectiveParity(): void
    {
        $template = '<head-meta /><head-tags />@head';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("head()->render()", $compiled);
    }

    public function testCompileFlashDirectivesAndTagParity(): void
    {
        $template = '<flash mode="toast" position="bottom-right" />@flash<s-flash mode="alert" />';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("flash_render('toast'", $compiled);
        $this->assertStringContainsString("flash_render()", $compiled);
        $this->assertStringContainsString("flash_render('alert'", $compiled);
    }

    public function testCompileOldAndJsonTagAndDirectiveParity(): void
    {
        $template = '<old name="email" default="default@test.com" />@old(\'username\')<json :data="$payload" />@json($data)';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("old('email', 'default@test.com')", $compiled);
        $this->assertStringContainsString("old('username'", $compiled);
        $this->assertStringContainsString("safeJson(\$payload)", $compiled);
        $this->assertStringContainsString("safeJson(\$data)", $compiled);
    }

    public function testCompileAuthAndGuestTagAndDirectiveParity(): void
    {
        $templateTag = '<auth><p>Logged in</p></auth><guest><p>Guest</p></guest>';
        $templateDir = '@auth<p>Logged in</p>@endauth@guest<p>Guest</p>@endguest';

        $compiledTag = $this->compiler->compile($templateTag);
        $compiledDir = $this->compiler->compile($templateDir);

        $this->assertStringContainsString("auth()->check()", $compiledTag);
        $this->assertStringContainsString("!auth()->check()", $compiledTag);
        $this->assertStringContainsString("auth()->check()", $compiledDir);
        $this->assertStringContainsString("!auth()->check()", $compiledDir);
    }

    public function testCompileCanAndCannotTagAndDirectiveParity(): void
    {
        $template = '<can ability="edit-post" :args="[$post]"><button>Edit</button></can>@cannot(\'delete-post\', $post)<p>Denied</p>@endcannot';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("gate()->allows('edit-post', ...[\$post])", $compiled);
        $this->assertStringContainsString("!gate()->allows('delete-post', \$post)", $compiled);
    }

    public function testCompileErrorAndSessionTagAndDirectiveParity(): void
    {
        $template = '<error field="email"><span>{{ $message }}</span></error>@session(\'status\')<div>{{ $value }}</div>@endsession';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("errors()->has('email')", $compiled);
        $this->assertStringContainsString("session()->has('status')", $compiled);
    }

    public function testCompileEnvAndProductionTagAndDirectiveParity(): void
    {
        $template = '<env name="production"><span>Live</span></env><production><p>Prod only</p></production>@production<b>Live</b>@endproduction';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("app()->isEnv('production')", $compiled);
        $this->assertStringContainsString("app()->isProduction()", $compiled);
    }

    public function testCompileIfTagSyntaxAndDirectiveParity(): void
    {
        $template = '<if cond="$user"><p>Welcome</p><elseif cond="$guest"/><p>Guest</p><else/><p>Unknown</p></if>@if($active)<b>Active</b>@elseif($pending)<i>Pending</i>@else<em>Offline</em>@endif';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString('<?php if ($user): ?>', $compiled);
        $this->assertStringContainsString('<?php elseif ($guest): ?>', $compiled);
        $this->assertStringContainsString('<?php else: ?>', $compiled);
        $this->assertStringContainsString('<?php endif; ?>', $compiled);

        $this->assertStringContainsString('<?php if ($active): ?>', $compiled);
        $this->assertStringContainsString('<?php elseif ($pending): ?>', $compiled);
    }

    public function testCompileUnlessTagSyntaxAndDirectiveParity(): void
    {
        $template = '<unless cond="$isLoggedIn"><a href="/login">Login</a></unless>@unless($isAdmin)<p>User</p>@endunless';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString('<?php if (!($isLoggedIn)): ?>', $compiled);
        $this->assertStringContainsString('<?php if (!($isAdmin)): ?>', $compiled);
    }

    public function testCompileIssetAndEmptyTagAndDirectiveParity(): void
    {
        $template = '<isset var="$record"><span>Found</span></isset>@empty($items)<p>None</p>@endempty';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString('<?php if (isset($record)): ?>', $compiled);
        $this->assertStringContainsString('<?php if (empty($items)): ?>', $compiled);
    }

    public function testCompileForeachAndForelseTagAndDirectiveParity(): void
    {
        $template = '<foreach items="$items" as="$item"><li>{{ $item }}</li></foreach>@forelse($users as $user)<p>{{ $user }}</p>@empty<p>No users</p>@endforelse';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString('<?php foreach ($items as $item): ?>', $compiled);
        $this->assertStringContainsString('<?php if (!empty($users)): foreach ($users as $user): ?>', $compiled);
        $this->assertStringContainsString('<?php endforeach; else: ?>', $compiled);
    }

    public function testCompileForAndWhileTagSyntaxAndDirectiveParity(): void
    {
        $template = '<for var="$i=0" cond="$i<3" incr="$i++"><span>{{ $i }}</span></for>@while($loading)<p>Loading...</p>@endwhile';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString('<?php for ($i=0; $i<3; $i++): ?>', $compiled);
        $this->assertStringContainsString('<?php while ($loading): ?>', $compiled);
    }

    public function testCompileSwitchTagAndDirectiveParity(): void
    {
        $template = '<switch value="$role"><case value="\'admin\'">Admin</case><break /><default>User</default></switch>';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString('<?php switch ($role): ?>', $compiled);
        $this->assertStringContainsString("<?php case 'admin': ?>", $compiled);
        $this->assertStringContainsString('<?php break; ?>', $compiled);
        $this->assertStringContainsString('<?php default: ?>', $compiled);
        $this->assertStringContainsString('<?php endswitch; ?>', $compiled);
    }

    public function testCompileLayoutsAndSectionsAndDirectives(): void
    {
        $template = '<extends name="layouts.app" /><section name="content"><h1>Page Title</h1></section>@yield(\'content\')@section(\'title\', \'My Title\')';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("<?php \$this->extend('layouts.app'); ?>", $compiled);
        $this->assertStringContainsString("<?php \$this->startSection('content'); ?>", $compiled);
        $this->assertStringContainsString("<?php \$this->endSection(); ?>", $compiled);
        $this->assertStringContainsString("<?= \$this->yieldSection('content', ''); ?>", $compiled);
        $this->assertStringContainsString("startSection('title')", $compiled);
    }

    public function testCompileDotSyntaxAccessForInterpolation(): void
    {
        $template = '{{ $user.name }}';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("\$this->get(\$user, 'name')", $compiled);
    }

    public function testCompileNestedDotSyntaxAccess(): void
    {
        $template = '{{ $user.profile.avatar }}';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("\$this->get(\$this->get(\$user, 'profile'), 'avatar')", $compiled);
    }

    public function testDotSyntaxInConditions(): void
    {
        $template = '<if cond="$user.isAdmin"><p>Admin</p></if>';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("\$this->get(\$user, 'isAdmin')", $compiled);
    }

    public function testPlainVariablesUnchangedByDotSyntax(): void
    {
        $template = '{{ $user }}';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString('($user)', $compiled);
    }

    public function testCompileContextDirectivesAndTags(): void
    {
        $template = '<context name="theme" :value="[\'mode\' => \'dark\']"><use-context name="theme" as="$theme" /></context>@context(\'auth\', [\'user\' => \'John\'])@useContext(\'auth\', \'$auth\')@endcontext';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("\\Switch\\Foundation\\Context\\Facade\\Context::context('theme')->push(['mode' => 'dark']);", $compiled);
        $this->assertStringContainsString("\$theme = \\Switch\\Foundation\\Context\\Facade\\Context::use('theme');", $compiled);
        $this->assertStringContainsString("\\Switch\\Foundation\\Context\\Facade\\Context::context('auth')->push(['user' => 'John']);", $compiled);
        $this->assertStringContainsString("\$auth = \\Switch\\Foundation\\Context\\Facade\\Context::use('auth');", $compiled);
    }

    public function testCompileDataAndMockDirectivesAndTags(): void
    {
        $template = '<data source="countries" as="$countries" /><data mock="user" count="5" as="$users" />@data(\'plans\', \'$plans\')@mock(\'post\', 3, \'$posts\')';
        $compiled = $this->compiler->compile($template);

        $this->assertStringContainsString("\$countries = function_exists('data') ? data('countries') : [];", $compiled);
        $this->assertStringContainsString("\$users = function_exists('mock') ? mock('user', 5) : [];", $compiled);
        $this->assertStringContainsString("\$plans = function_exists('data') ? data('plans') : [];", $compiled);
        $this->assertStringContainsString("\$posts = function_exists('mock') ? mock('post', 3) : [];", $compiled);
    }
}

