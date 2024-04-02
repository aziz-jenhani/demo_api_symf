<?php

namespace App\EventListener;

use App\Exception\Jwt\ExpiredTokenException;
use App\Exception\Jwt\InvalidTokenException;
use App\Exception\UnauthorizedException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JwtAuthenticationListener implements EventSubscriberInterface
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $data['accessToken'] = $data['token'];
        unset($data['token']);
        $event->setData($data);
    }

    public function onJWTInvalid(JWTInvalidEvent $event)
    {
        throw new InvalidTokenException();
    }

    public function onJWTNotFound(JWTNotFoundEvent $event)
    {
        throw new UnauthorizedException();
    }

    public function onJWTExpired(JWTExpiredEvent $event)
    {
         throw new ExpiredTokenException();
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::JWT_INVALID => 'onJWTInvalid',
            Events::JWT_NOT_FOUND => 'onJWTNotFound',
            Events::JWT_EXPIRED => 'onJWTExpired',
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccessResponse'
        ];
    }
}
