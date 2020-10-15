<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzSupportTools\EventListener;

use eZ\Publish\API\Repository\PermissionResolver;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use EzSystems\EzPlatformAdminUi\Menu\MenuItemFactory;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigureMainMenuListener implements EventSubscriberInterface, TranslationContainerInterface
{
    public const ITEM_ADMIN__SYSTEMINFO = 'main__admin__systeminfo';

    /** @var \EzSystems\EzPlatformAdminUi\Menu\MenuItemFactory */
    private $menuItemFactory;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(
        MenuItemFactory $menuItemFactory,
        PermissionResolver $permissionResolver
    ) {
        $this->menuItemFactory = $menuItemFactory;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        if (!$this->permissionResolver->hasAccess('setup', 'system_info')) {
            return;
        }

        $menu->getChild(MainMenuBuilder::ITEM_ADMIN)->addChild(
            $this->menuItemFactory->createItem(
                self::ITEM_ADMIN__SYSTEMINFO,
                [
                    'route' => 'ezplatform.systeminfo',
                    'extras' => [
                        'orderNumber' => 10,
                    ],
                ],
            )
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => 'onMenuConfigure',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_ADMIN__SYSTEMINFO, 'menu'))->setDesc('System Information'),
        ];
    }
}
