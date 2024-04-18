<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RefreshTokenRepository")
 * @ORM\Table(name="refresh_tokens") // Specify the table name explicitly
 */
#[ORM\Entity]
#[ORM\Table(name: 'refresh_tokens')]
class RefreshToken extends BaseRefreshToken
{
}
