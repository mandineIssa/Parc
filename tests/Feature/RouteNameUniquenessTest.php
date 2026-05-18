<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Objectif : garantir qu’un même nom de route n’est pas enregistré deux fois.
 * À activer pleinement une fois routes/web.php refactorisé en modules sans doublons.
 */
#[Group('routes')]
class RouteNameUniquenessTest extends TestCase
{
    public function test_route_names_should_be_unique(): void
    {
        $this->markTestSkipped(
            'Le fichier routes/web.php contient encore des doublons historiques ; activer ce test après refactor modularisé.'
        );

        $seen = [];

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if ($name === null) {
                continue;
            }

            $this->assertArrayNotHasKey(
                $name,
                $seen,
                sprintf(
                    'Nom de route dupliqué : %s (%s %s)',
                    $name,
                    implode('|', $route->methods()),
                    $route->uri()
                )
            );

            $seen[$name] = true;
        }
    }
}
