<?php

namespace NotificationChannels\Discord\Tests;

use Mockery;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Config\Repository as Config;
use NotificationChannels\Discord\Discord;
use NotificationChannels\Discord\Commands\SetupCommand;
use NotificationChannels\Discord\DiscordServiceProvider;

class DiscordServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_loads_the_setup_command_and_provides_the_discord_token()
    {
        $app = Mockery::mock(Application::class);
        $config = Mockery::mock(Config::class);

        $config->shouldReceive('get')->with('services.discord.token')->once()->andReturn('a-secret-key');

        $app->shouldReceive('bind')->with('command.discord:setup', SetupCommand::class)->once();
        $app->shouldReceive('runningInConsole')->once()->andReturn(true);

        $app->shouldReceive('make')->with('config')->once()->andReturn($config);
        $app->shouldReceive('when')->with(Discord::class)->once()->andReturn($app);
        $app->shouldReceive('needs')->with('$token')->once()->andReturn($app);
        $app->shouldReceive('give')->with('a-secret-key')->once();

        $provider = Mockery::mock(DiscordServiceProvider::class.'[commands]', [$app]);

        $provider->shouldReceive('commands')->with('command.discord:setup')->once();

        $provider->register();
        $provider->boot();
    }
}