<?php

namespace Coolsam\VisualForms;

use Coolsam\VisualForms\Models\VisualFormComponent;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\SplFileInfo;

class Utils
{
    public static function getFileNamespace(SplFileInfo $file, $baseNamespace = 'Coolsam\\VisualForms'): string
    {
        $namespace = $baseNamespace;
        $path = $file->getRelativePath();
        if ($path) {
            $namespace .= '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $path);
        }
        $namespace .= '\\' . $file->getBasename('.php');

        return $namespace;
    }

    public static function instantiateClass(string $namespace, $args = [])
    {
        return new $namespace(...$args);
    }

    public static function getBool(mixed $boolValue): bool
    {
        return filter_var($boolValue, FILTER_VALIDATE_BOOLEAN);
    }

    public static function getHeroicons(): Collection
    {
        $heroicons = $heroicons = [
            'heroicon-o-academic-cap',
            'heroicon-o-adjustments-horizontal',
            'heroicon-o-adjustments-vertical',
            'heroicon-o-archive-box-arrow-down',
            'heroicon-o-archive-box-x-mark',
            'heroicon-o-archive-box',
            'heroicon-o-arrow-down-circle',
            'heroicon-o-arrow-down-left',
            'heroicon-o-arrow-down-on-square-stack',
            'heroicon-o-arrow-down-on-square',
            'heroicon-o-arrow-down-right',
            'heroicon-o-arrow-down-tray',
            'heroicon-o-arrow-down',
            'heroicon-o-arrow-left-circle',
            'heroicon-o-arrow-left-on-rectangle',
            'heroicon-o-arrow-left',
            'heroicon-o-arrow-long-down',
            'heroicon-o-arrow-long-left',
            'heroicon-o-arrow-long-right',
            'heroicon-o-arrow-long-up',
            'heroicon-o-arrow-path-rounded-square',
            'heroicon-o-arrow-path',
            'heroicon-o-arrow-right-circle',
            'heroicon-o-arrow-right-on-rectangle',
            'heroicon-o-arrow-right',
            'heroicon-o-arrow-small-down',
            'heroicon-o-arrow-small-left',
            'heroicon-o-arrow-small-right',
            'heroicon-o-arrow-small-up',
            'heroicon-o-arrow-top-right-on-square',
            'heroicon-o-arrow-trending-down',
            'heroicon-o-arrow-trending-up',
            'heroicon-o-arrow-up-circle',
            'heroicon-o-arrow-up-left',
            'heroicon-o-arrow-up-on-square-stack',
            'heroicon-o-arrow-up-on-square',
            'heroicon-o-arrow-up-right',
            'heroicon-o-arrow-up-tray',
            'heroicon-o-arrow-up',
            'heroicon-o-arrow-uturn-down',
            'heroicon-o-arrow-uturn-left',
            'heroicon-o-arrow-uturn-right',
            'heroicon-o-arrow-uturn-up',
            'heroicon-o-arrows-pointing-in',
            'heroicon-o-arrows-pointing-out',
            'heroicon-o-arrows-right-left',
            'heroicon-o-arrows-up-down',
            'heroicon-o-at-symbol',
            'heroicon-o-backspace',
            'heroicon-o-backward',
            'heroicon-o-banknotes',
            'heroicon-o-bars-2',
            'heroicon-o-bars-3-bottom-left',
            'heroicon-o-bars-3-bottom-right',
            'heroicon-o-bars-3-center-left',
            'heroicon-o-bars-3',
            'heroicon-o-bars-4',
            'heroicon-o-bars-arrow-down',
            'heroicon-o-bars-arrow-up',
            'heroicon-o-battery-0',
            'heroicon-o-battery-100',
            'heroicon-o-battery-50',
            'heroicon-o-beaker',
            'heroicon-o-bell-alert',
            'heroicon-o-bell-slash',
            'heroicon-o-bell-snooze',
            'heroicon-o-bell',
            'heroicon-o-bolt-slash',
            'heroicon-o-bolt',
            'heroicon-o-book-open',
            'heroicon-o-bookmark-slash',
            'heroicon-o-bookmark-square',
            'heroicon-o-bookmark',
            'heroicon-o-briefcase',
            'heroicon-o-bug-ant',
            'heroicon-o-building-library',
            'heroicon-o-building-office-2',
            'heroicon-o-building-office',
            'heroicon-o-building-storefront',
            'heroicon-o-cake',
            'heroicon-o-calculator',
            'heroicon-o-calendar-days',
            'heroicon-o-calendar',
            'heroicon-o-camera',
            'heroicon-o-chart-bar-square',
            'heroicon-o-chart-bar',
            'heroicon-o-chart-pie',
            'heroicon-o-chat-bubble-bottom-center-text',
            'heroicon-o-chat-bubble-bottom-center',
            'heroicon-o-chat-bubble-left-ellipsis',
            'heroicon-o-chat-bubble-left-right',
            'heroicon-o-chat-bubble-left',
            'heroicon-o-chat-bubbles',
            'heroicon-o-check-badge',
            'heroicon-o-check-circle',
            'heroicon-o-check',
            'heroicon-o-chevron-double-down',
            'heroicon-o-chevron-double-left',
            'heroicon-o-chevron-double-right',
            'heroicon-o-chevron-double-up',
            'heroicon-o-chevron-down',
            'heroicon-o-chevron-left',
            'heroicon-o-chevron-right',
            'heroicon-o-chevron-up-down',
            'heroicon-o-chevron-up',
            'heroicon-o-circle-stack',
            'heroicon-o-clipboard-document-check',
            'heroicon-o-clipboard-document-list',
            'heroicon-o-clipboard-document',
            'heroicon-o-clipboard',
            'heroicon-o-clock',
            'heroicon-o-cloud-arrow-down',
            'heroicon-o-cloud-arrow-up',
            'heroicon-o-cloud',
            'heroicon-o-code-bracket-square',
            'heroicon-o-code-bracket',
            'heroicon-o-cog-6-tooth',
            'heroicon-o-cog-8-tooth',
            'heroicon-o-cog',
            'heroicon-o-command-line',
            'heroicon-o-computer-desktop',
            'heroicon-o-cpu-chip',
            'heroicon-o-credit-card',
            'heroicon-o-cube-transparent',
            'heroicon-o-cube',
            'heroicon-o-currency-bangladeshi',
            'heroicon-o-currency-dollar',
            'heroicon-o-currency-euro',
            'heroicon-o-currency-pound',
            'heroicon-o-currency-rupee',
            'heroicon-o-currency-yen',
            'heroicon-o-cursor-arrow-rays',
            'heroicon-o-cursor-arrow-ripple',
            'heroicon-o-device-phone-mobile',
            'heroicon-o-device-tablet',
            'heroicon-o-document-arrow-down',
            'heroicon-o-document-arrow-up',
            'heroicon-o-document-chart-bar',
            'heroicon-o-document-chart-line',
            'heroicon-o-document-check',
            'heroicon-o-document-duplicate',
            'heroicon-o-document-magnifying-glass',
            'heroicon-o-document-minus',
            'heroicon-o-document-plus',
            'heroicon-o-document-text',
            'heroicon-o-document',
            'heroicon-o-ellipsis-horizontal-circle',
            'heroicon-o-ellipsis-horizontal',
            'heroicon-o-ellipsis-vertical',
            'heroicon-o-envelope-open',
            'heroicon-o-envelope',
            'heroicon-o-exclamation-circle',
            'heroicon-o-exclamation-triangle',
            'heroicon-o-eye-dropper',
            'heroicon-o-eye-slash',
            'heroicon-o-eye',
            'heroicon-o-face-frown',
            'heroicon-o-face-smile',
            'heroicon-o-film',
            'heroicon-o-finger-print',
            'heroicon-o-fire',
            'heroicon-o-flag',
            'heroicon-o-folder-arrow-down',
            'heroicon-o-folder-minus',
            'heroicon-o-folder-open',
            'heroicon-o-folder-plus',
            'heroicon-o-folder',
            'heroicon-o-forward',
            'heroicon-o-funnel',
            'heroicon-o-gif',
            'heroicon-o-gift-top',
            'heroicon-o-gift',
            'heroicon-o-globe-alt',
            'heroicon-o-globe-americas',
            'heroicon-o-globe-asia-australia',
            'heroicon-o-globe-europe-africa',
            'heroicon-o-hand-raised',
            'heroicon-o-hand-thumb-down',
            'heroicon-o-hand-thumb-up',
            'heroicon-o-hashtag',
            'heroicon-o-heart',
            'heroicon-o-home-modern',
            'heroicon-o-home',
            'heroicon-o-identification',
            'heroicon-o-inbox-arrow-down',
            'heroicon-o-inbox-stack',
            'heroicon-o-inbox',
            'heroicon-o-information-circle',
            'heroicon-o-key',
            'heroicon-o-language',
            'heroicon-o-lifebuoy',
            'heroicon-o-light-bulb',
            'heroicon-o-link',
            'heroicon-o-list-bullet',
            'heroicon-o-lock-closed',
            'heroicon-o-lock-open',
            'heroicon-o-magnifying-glass-circle',
            'heroicon-o-magnifying-glass-minus',
            'heroicon-o-magnifying-glass-plus',
            'heroicon-o-magnifying-glass',
            'heroicon-o-map-pin',
            'heroicon-o-map',
            'heroicon-o-megaphone',
            'heroicon-o-microphone',
            'heroicon-o-minus-circle',
            'heroicon-o-minus-small',
            'heroicon-o-minus',
            'heroicon-o-moon',
            'heroicon-o-musical-note',
            'heroicon-o-newspaper',
            'heroicon-o-no-symbol',
            'heroicon-o-paint-brush',
            'heroicon-o-paper-airplane',
            'heroicon-o-paper-clip',
            'heroicon-o-pause-circle',
            'heroicon-o-pause',
            'heroicon-o-pencil-square',
            'heroicon-o-pencil',
            'heroicon-o-phone-arrow-down-left',
            'heroicon-o-phone-arrow-up-right',
            'heroicon-o-phone-x-mark',
            'heroicon-o-phone',
            'heroicon-o-photo',
            'heroicon-o-play-circle',
            'heroicon-o-play-pause',
            'heroicon-o-play',
            'heroicon-o-plus-circle',
            'heroicon-o-plus-small',
            'heroicon-o-plus',
            'heroicon-o-presentation-chart-bar',
            'heroicon-o-presentation-chart-line',
            'heroicon-o-printer',
            'heroicon-o-puzzle-piece',
            'heroicon-o-qr-code',
            'heroicon-o-question-mark-circle',
            'heroicon-o-queue-list',
            'heroicon-o-radio',
            'heroicon-o-receipt-percent',
            'heroicon-o-receipt-refund',
            'heroicon-o-rectangle-group',
            'heroicon-o-rectangle-stack',
            'heroicon-o-rocket-launch',
            'heroicon-o-rss',
            'heroicon-o-scale',
            'heroicon-o-scissors',
            'heroicon-o-server-stack',
            'heroicon-o-server',
            'heroicon-o-share',
            'heroicon-o-shield-check',
            'heroicon-o-shield-exclamation',
            'heroicon-o-shopping-bag',
            'heroicon-o-shopping-cart',
            'heroicon-o-signal-slash',
            'heroicon-o-signal',
            'heroicon-o-sparkles',
            'heroicon-o-speaker-wave',
            'heroicon-o-speaker-x-mark',
            'heroicon-o-square-2-stack',
            'heroicon-o-square-3-stack-3d',
            'heroicon-o-squares-2x2',
            'heroicon-o-squares-plus',
            'heroicon-o-star',
            'heroicon-o-stop-circle',
            'heroicon-o-stop',
            'heroicon-o-sun',
            'heroicon-o-swatch',
            'heroicon-o-table-cells',
            'heroicon-o-tag',
            'heroicon-o-ticket',
            'heroicon-o-trash',
            'heroicon-o-trophy',
            'heroicon-o-truck',
            'heroicon-o-tv',
            'heroicon-o-user-circle',
            'heroicon-o-user-group',
            'heroicon-o-user-minus',
            'heroicon-o-user-plus',
            'heroicon-o-user',
            'heroicon-o-users',
            'heroicon-o-variable',
            'heroicon-o-video-camera-slash',
            'heroicon-o-video-camera',
            'heroicon-o-view-columns',
            'heroicon-o-viewfinder-circle',
            'heroicon-o-wallet',
            'heroicon-o-wifi',
            'heroicon-o-window',
            'heroicon-o-wrench-screwdriver',
            'heroicon-o-wrench',
            'heroicon-o-x-circle',
            'heroicon-o-x-mark',
            'heroicon-o-bars-3-center-left',
            'heroicon-o-bars-3-bottom-left',
            'heroicon-o-bars-3-bottom-right',
            'heroicon-o-bars-3',
            'heroicon-o-bars-4',
            'heroicon-o-bars-arrow-down',
            'heroicon-o-bars-arrow-up',
            'heroicon-o-battery-0',
            'heroicon-o-battery-100',
            'heroicon-o-battery-50',
            'heroicon-o-beaker',
            'heroicon-o-bell-alert',
            'heroicon-o-bell-slash',
            'heroicon-o-bell-snooze',
            'heroicon-o-bell',
            'heroicon-o-bolt-slash',
            'heroicon-o-bolt',
            'heroicon-o-book-open',
            'heroicon-o-bookmark-slash',
            'heroicon-o-bookmark-square',
            'heroicon-o-bookmark',
            'heroicon-o-briefcase',
            'heroicon-o-bug-ant',
            'heroicon-o-building-library',
            'heroicon-o-building-office-2',
            'heroicon-o-building-office',
            'heroicon-o-building-storefront',
            'heroicon-o-cake',
            'heroicon-o-calculator',
            'heroicon-o-calendar-days',
            'heroicon-o-calendar',
            'heroicon-o-camera',
            'heroicon-o-chart-bar-square',
            'heroicon-o-chart-bar',
            'heroicon-o-chart-pie',
            'heroicon-o-chat-bubble-bottom-center-text',
            'heroicon-o-chat-bubble-bottom-center',
            'heroicon-o-chat-bubble-left-ellipsis',
            'heroicon-o-chat-bubble-left-right',
            'heroicon-o-chat-bubble-left',
            'heroicon-o-chat-bubbles',
            'heroicon-o-check-badge',
            'heroicon-o-check-circle',
            'heroicon-o-check',
            'heroicon-o-chevron-double-down',
            'heroicon-o-chevron-double-left',
            'heroicon-o-chevron-double-right',
            'heroicon-o-chevron-double-up',
            'heroicon-o-chevron-down',
            'heroicon-o-chevron-left',
            'heroicon-o-chevron-right',
            'heroicon-o-chevron-up-down',
            'heroicon-o-chevron-up',
            'heroicon-o-circle-stack',
            'heroicon-o-clipboard-document-check',
            'heroicon-o-clipboard-document-list',
            'heroicon-o-clipboard-document',
            'heroicon-o-clipboard',
            'heroicon-o-clock',
            'heroicon-o-cloud-arrow-down',
            'heroicon-o-cloud-arrow-up',
            'heroicon-o-cloud',
            'heroicon-o-code-bracket-square',
            'heroicon-o-code-bracket',
            'heroicon-o-cog-6-tooth',
            'heroicon-o-cog-8-tooth',
            'heroicon-o-cog',
            'heroicon-o-command-line',
            'heroicon-o-computer-desktop',
            'heroicon-o-cpu-chip',
            'heroicon-o-credit-card',
            'heroicon-o-cube-transparent',
            'heroicon-o-cube',
            'heroicon-o-currency-bangladeshi',
            'heroicon-o-currency-dollar',
            'heroicon-o-currency-euro',
            'heroicon-o-currency-pound',
            'heroicon-o-currency-rupee',
            'heroicon-o-currency-yen',
            'heroicon-o-cursor-arrow-rays',
            'heroicon-o-cursor-arrow-ripple',
            'heroicon-o-device-phone-mobile',
            'heroicon-o-device-tablet',
            'heroicon-o-document-arrow-down',
            'heroicon-o-document-arrow-up',
            'heroicon-o-document-chart-bar',
            'heroicon-o-document-chart-line',
            'heroicon-o-document-check',
            'heroicon-o-document-duplicate',
            'heroicon-o-document-magnifying-glass',
            'heroicon-o-document-minus',
            'heroicon-o-document-plus',
            'heroicon-o-document-text',
            'heroicon-o-document',
            'heroicon-o-ellipsis-horizontal-circle',
            'heroicon-o-ellipsis-horizontal',
            'heroicon-o-ellipsis-vertical',
            'heroicon-o-envelope-open',
            'heroicon-o-envelope',
            'heroicon-o-exclamation-circle',
            'heroicon-o-exclamation-triangle',
            'heroicon-o-eye-dropper',
            'heroicon-o-eye-slash',
            'heroicon-o-eye',
            'heroicon-o-face-frown',
            'heroicon-o-face-smile',
            'heroicon-o-film',
            'heroicon-o-finger-print',
            'heroicon-o-fire',
            'heroicon-o-flag',
            'heroicon-o-folder-arrow-down',
            'heroicon-o-folder-minus',
            'heroicon-o-folder-open',
            'heroicon-o-folder-plus',
            'heroicon-o-folder',
            'heroicon-o-forward',
            'heroicon-o-funnel',
            'heroicon-o-gif',
            'heroicon-o-gift-top',
            'heroicon-o-gift',
            'heroicon-o-globe-alt',
            'heroicon-o-globe-americas',
            'heroicon-o-globe-asia-australia',
            'heroicon-o-globe-europe-africa',
            'heroicon-o-hand-raised',
            'heroicon-o-hand-thumb-down',
            'heroicon-o-hand-thumb-up',
            'heroicon-o-hashtag',
            'heroicon-o-heart',
            'heroicon-o-home-modern',
            'heroicon-o-home',
            'heroicon-o-identification',
            'heroicon-o-inbox-arrow-down',
            'heroicon-o-inbox-stack',
            'heroicon-o-inbox',
            'heroicon-o-information-circle',
            'heroicon-o-key',
            'heroicon-o-language',
            'heroicon-o-lifebuoy',
            'heroicon-o-light-bulb',
            'heroicon-o-link',
            'heroicon-o-list-bullet',
            'heroicon-o-lock-closed',
            'heroicon-o-lock-open',
            'heroicon-o-magnifying-glass-circle',
            'heroicon-o-magnifying-glass-minus',
            'heroicon-o-magnifying-glass-plus',
            'heroicon-o-magnifying-glass',
            'heroicon-o-map-pin',
            'heroicon-o-map',
            'heroicon-o-megaphone',
            'heroicon-o-microphone',
            'heroicon-o-minus-circle',
            'heroicon-o-minus-small',
            'heroicon-o-minus',
            'heroicon-o-moon',
            'heroicon-o-musical-note',
            'heroicon-o-newspaper',
            'heroicon-o-no-symbol',
            'heroicon-o-paint-brush',
            'heroicon-o-paper-airplane',
            'heroicon-o-paper-clip',
            'heroicon-o-pause-circle',
            'heroicon-o-pause',
            'heroicon-o-pencil-square',
            'heroicon-o-pencil',
            //
            'heroicon-o-pencil-square',
            'heroicon-o-phone-arrow-down-left',
            'heroicon-o-phone-arrow-up-right',
            'heroicon-o-phone-x-mark',
            'heroicon-o-phone',
            'heroicon-o-photo',
            'heroicon-o-play-circle',
            'heroicon-o-play-pause',
            'heroicon-o-play',
            'heroicon-o-plus-circle',
            'heroicon-o-plus-small',
            'heroicon-o-plus',
            'heroicon-o-presentation-chart-bar',
            'heroicon-o-presentation-chart-line',
            'heroicon-o-printer',
            'heroicon-o-puzzle-piece',
            'heroicon-o-qr-code',
            'heroicon-o-question-mark-circle',
            'heroicon-o-queue-list',
            'heroicon-o-radio',
            'heroicon-o-receipt-percent',
            'heroicon-o-receipt-refund',
            'heroicon-o-rectangle-group',
            'heroicon-o-rectangle-stack',
            'heroicon-o-rocket-launch',
            'heroicon-o-rss',
            'heroicon-o-scale',
            'heroicon-o-scissors',
            'heroicon-o-server-stack',
            'heroicon-o-server',
            'heroicon-o-share',
            'heroicon-o-shield-check',
            'heroicon-o-shield-exclamation',
            'heroicon-o-shopping-bag',
            'heroicon-o-shopping-cart',
            'heroicon-o-signal-slash',
            'heroicon-o-signal',
            'heroicon-o-sparkles',
            'heroicon-o-speaker-wave',
            'heroicon-o-speaker-x-mark',
            'heroicon-o-square-2-stack',
            'heroicon-o-square-3-stack-3d',
            'heroicon-o-squares-2x2',
            'heroicon-o-squares-plus',
            'heroicon-o-star',
            'heroicon-o-stop-circle',
            'heroicon-o-stop',
            'heroicon-o-sun',
            'heroicon-o-swatch',
            'heroicon-o-table-cells',
            'heroicon-o-tag',
            'heroicon-o-ticket',
            'heroicon-o-trash',
            'heroicon-o-trophy',
            'heroicon-o-truck',
            'heroicon-o-tv',
            'heroicon-o-user-circle',
            'heroicon-o-user-group',
            'heroicon-o-user-minus',
            'heroicon-o-user-plus',
            'heroicon-o-user',
            'heroicon-o-users',
            'heroicon-o-variable',
            'heroicon-o-video-camera-slash',
            'heroicon-o-video-camera',
            'heroicon-o-view-columns',
            'heroicon-o-viewfinder-circle',
            'heroicon-o-wallet',
            'heroicon-o-wifi',
            'heroicon-o-window',
            'heroicon-o-wrench-screwdriver',
            'heroicon-o-wrench',
            'heroicon-o-x-circle',
            'heroicon-o-x-mark',
            'heroicon-s-academic-cap',
            'heroicon-s-adjustments-horizontal',
            'heroicon-s-adjustments-vertical',
            'heroicon-s-archive-box-arrow-down',
            'heroicon-s-archive-box-x-mark',
            'heroicon-s-archive-box',
            'heroicon-s-arrow-down-circle',
            'heroicon-s-arrow-down-left',
            'heroicon-s-arrow-down-on-square-stack',
            'heroicon-s-arrow-down-on-square',
            'heroicon-s-arrow-down-right',
            'heroicon-s-arrow-down-tray',
            'heroicon-s-arrow-down',
            'heroicon-s-arrow-left-circle',
            'heroicon-s-arrow-left-on-rectangle',
            'heroicon-s-arrow-left',
            'heroicon-s-arrow-long-down',
            'heroicon-s-arrow-long-left',
            'heroicon-s-arrow-long-right',
            'heroicon-s-arrow-long-up',
            'heroicon-s-arrow-path-rounded-square',
            'heroicon-s-arrow-path',
            'heroicon-s-arrow-right-circle',
            'heroicon-s-arrow-right-on-rectangle',
            'heroicon-s-arrow-right',
            'heroicon-s-arrow-small-down',
            'heroicon-s-arrow-small-left',
            'heroicon-s-arrow-small-right',
            'heroicon-s-arrow-small-up',
            'heroicon-s-arrow-top-right-on-square',
            'heroicon-s-arrow-trending-down',
            'heroicon-s-arrow-trending-up',
            'heroicon-s-arrow-up-circle',
            'heroicon-s-arrow-up-left',
            'heroicon-s-arrow-up-on-square-stack',
            'heroicon-s-arrow-up-on-square',
            'heroicon-s-arrow-up-right',
            'heroicon-s-arrow-up-tray',
            'heroicon-s-arrow-up',
            'heroicon-s-arrow-uturn-down',
            'heroicon-s-arrow-uturn-left',
            'heroicon-s-arrow-uturn-right',
            'heroicon-s-arrow-uturn-up',
            'heroicon-s-arrows-pointing-in',
            'heroicon-s-arrows-pointing-out',
            'heroicon-s-arrows-right-left',
            'heroicon-s-arrows-up-down',
            'heroicon-s-at-symbol',
            'heroicon-s-backspace',
            'heroicon-s-backward',
            'heroicon-s-banknotes',
            'heroicon-s-bars-2',
            'heroicon-s-bars-3-bottom-left',
            'heroicon-s-bars-3-bottom-right',
            'heroicon-s-bars-3-center-left',
            'heroicon-s-bars-3',
            'heroicon-s-bars-4',
            'heroicon-s-bars-arrow-down',
            'heroicon-s-bars-arrow-up',
            'heroicon-s-battery-0',
            'heroicon-s-battery-100',
            'heroicon-s-battery-50',
            'heroicon-s-beaker',
            'heroicon-s-bell-alert',
            'heroicon-s-bell-slash',
            'heroicon-s-bell-snooze',
            'heroicon-s-bell',
            'heroicon-s-bolt-slash',
            'heroicon-s-bolt',
            'heroicon-s-book-open',
            'heroicon-s-bookmark-slash',
            'heroicon-s-bookmark-square',
            'heroicon-s-bookmark',
            'heroicon-s-briefcase',
            'heroicon-s-bug-ant',
            'heroicon-s-building-library',
            'heroicon-s-building-office-2',
            'heroicon-s-building-office',
            'heroicon-s-building-storefront',
            'heroicon-s-cake',
            'heroicon-s-calculator',
            'heroicon-s-calendar-days',
            'heroicon-s-calendar',
            'heroicon-s-camera',
            'heroicon-s-chart-bar-square',
            'heroicon-s-chart-bar',
            'heroicon-s-chart-pie',
            'heroicon-s-chat-bubble-bottom-center-text',
            'heroicon-s-chat-bubble-bottom-center',
            'heroicon-s-chat-bubble-left-ellipsis',
            'heroicon-s-chat-bubble-left-right',
            'heroicon-s-chat-bubble-left',
            'heroicon-s-chat-bubbles',
            'heroicon-s-check-badge',
            'heroicon-s-check-circle',
            'heroicon-s-check',
            'heroicon-s-chevron-double-down',
            'heroicon-s-chevron-double-left',
            'heroicon-s-chevron-double-right',
            'heroicon-s-chevron-double-up',
            'heroicon-s-chevron-down',
            'heroicon-s-chevron-left',
            'heroicon-s-chevron-right',
            'heroicon-s-chevron-up-down',
            'heroicon-s-chevron-up',
            'heroicon-s-circle-stack',
            'heroicon-s-clipboard-document-check',
            'heroicon-s-clipboard-document-list',
            'heroicon-s-clipboard-document',
            'heroicon-s-clipboard',
            'heroicon-s-clock',
            'heroicon-s-cloud-arrow-down',
            'heroicon-s-cloud-arrow-up',
            'heroicon-s-cloud',
            'heroicon-s-code-bracket-square',
            'heroicon-s-code-bracket',
            'heroicon-s-cog-6-tooth',
            'heroicon-s-cog-8-tooth',
            'heroicon-s-cog',
            'heroicon-s-command-line',
            'heroicon-s-computer-desktop',
            'heroicon-s-cpu-chip',
            'heroicon-s-credit-card',
            'heroicon-s-cube-transparent',
            'heroicon-s-cube',
            'heroicon-s-currency-bangladeshi',
            'heroicon-s-currency-dollar',
            'heroicon-s-currency-euro',
            'heroicon-s-currency-pound',
            'heroicon-s-currency-rupee',
            'heroicon-s-currency-yen',
            'heroicon-s-cursor-arrow-rays',
            'heroicon-s-cursor-arrow-ripple',
            'heroicon-s-device-phone-mobile',
            'heroicon-s-device-tablet',
            'heroicon-s-document-arrow-down',
            'heroicon-s-document-arrow-up',
            'heroicon-s-document-chart-bar',
            'heroicon-s-document-chart-line',
            'heroicon-s-document-check',
            'heroicon-s-document-duplicate',
            'heroicon-s-document-magnifying-glass',
            'heroicon-s-document-minus',
            'heroicon-s-document-plus',
            'heroicon-s-document-text',
            'heroicon-s-document',
            'heroicon-s-ellipsis-horizontal-circle',
            'heroicon-s-ellipsis-horizontal',
            'heroicon-s-ellipsis-vertical',
            'heroicon-s-envelope-open',
            'heroicon-s-envelope',
            'heroicon-s-exclamation-circle',
            'heroicon-s-exclamation-triangle',
            'heroicon-s-eye-dropper',
            'heroicon-s-eye-slash',
            'heroicon-s-eye',
            'heroicon-s-face-frown',
            'heroicon-s-face-smile',
            'heroicon-s-film',
            'heroicon-s-finger-print',
            'heroicon-s-fire',
            'heroicon-s-flag',
            'heroicon-s-folder-arrow-down',
            'heroicon-s-folder-minus',
            'heroicon-s-folder-open',
            'heroicon-s-folder-plus',
            'heroicon-s-folder',
            'heroicon-s-forward',
            'heroicon-s-funnel',
            'heroicon-s-gif',
            'heroicon-s-gift-top',
            'heroicon-s-gift',
            'heroicon-s-globe-alt',
            'heroicon-s-globe-americas',
            'heroicon-s-globe-asia-australia',
            'heroicon-s-globe-europe-africa',
            'heroicon-s-hand-raised',
            'heroicon-s-hand-thumb-down',
            'heroicon-s-hand-thumb-up',
            'heroicon-s-hashtag',
            'heroicon-s-heart',
            'heroicon-s-home-modern',
            'heroicon-s-home',
            'heroicon-s-identification',
            'heroicon-s-inbox-arrow-down',
            'heroicon-s-inbox-stack',
            'heroicon-s-inbox',
            'heroicon-s-information-circle',
            'heroicon-s-key',
            'heroicon-s-language',
            'heroicon-s-lifebuoy',
            'heroicon-s-light-bulb',
            'heroicon-s-link',
            'heroicon-s-list-bullet',
            'heroicon-s-lock-closed',
            'heroicon-s-lock-open',
            'heroicon-s-magnifying-glass-circle',
            'heroicon-s-magnifying-glass-minus',
            'heroicon-s-magnifying-glass-plus',
            'heroicon-s-magnifying-glass',
            'heroicon-s-map-pin',
            'heroicon-s-map',
            'heroicon-s-megaphone',
            'heroicon-s-microphone',
            'heroicon-s-minus-circle',
            'heroicon-s-minus-small',
            'heroicon-s-minus',
            'heroicon-s-moon',
            'heroicon-s-musical-note',
            'heroicon-s-newspaper',
            'heroicon-s-no-symbol',
            'heroicon-s-paint-brush',
            'heroicon-s-paper-airplane',
            'heroicon-s-paper-clip',
            'heroicon-s-pause-circle',
            'heroicon-s-pause',
            'heroicon-s-pencil-square',
            'heroicon-s-pencil',
            'heroicon-s-phone-arrow-down-left',
            'heroicon-s-phone-arrow-up-right',
            'heroicon-s-phone-x-mark',
            'heroicon-s-phone',
            'heroicon-s-photo',
            'heroicon-s-play-circle',
            'heroicon-s-play-pause',
            'heroicon-s-play',
            'heroicon-s-plus-circle',
            'heroicon-s-plus-small',
            'heroicon-s-plus',
            'heroicon-s-presentation-chart-bar',
            'heroicon-s-presentation-chart-line',
            'heroicon-s-printer',
            'heroicon-s-puzzle-piece',
            'heroicon-s-qr-code',
            'heroicon-s-question-mark-circle',
            'heroicon-s-queue-list',
            'heroicon-s-radio',
            'heroicon-s-receipt-percent',
            'heroicon-s-receipt-refund',
            'heroicon-s-rectangle-group',
            'heroicon-s-rectangle-stack',
            'heroicon-s-rocket-launch',
            'heroicon-s-rss',
            'heroicon-s-scale',
            'heroicon-s-scissors',
            'heroicon-s-server-stack',
            'heroicon-s-server',
            'heroicon-s-share',
            'heroicon-s-shield-check',
            'heroicon-s-shield-exclamation',
            'heroicon-s-shopping-bag',
            'heroicon-s-shopping-cart',
            'heroicon-s-signal-slash',
            'heroicon-s-signal',
            'heroicon-s-sparkles',
            'heroicon-s-speaker-wave',
            'heroicon-s-speaker-x-mark',
            'heroicon-s-square-2-stack',
            'heroicon-s-square-3-stack-3d',
            'heroicon-s-squares-2x2',
            'heroicon-s-squares-plus',
            'heroicon-s-star',
            'heroicon-s-stop-circle',
            'heroicon-s-stop',
            'heroicon-s-sun',
            'heroicon-s-swatch',
            'heroicon-s-table-cells',
            'heroicon-s-tag',
            'heroicon-s-ticket',
            'heroicon-s-trash',
            'heroicon-s-trophy',
            'heroicon-s-truck',
            'heroicon-s-tv',
            'heroicon-s-user-circle',
            'heroicon-s-user-group',
            'heroicon-s-user-minus',
            'heroicon-s-user-plus',
            'heroicon-s-user',
            'heroicon-s-users',
            'heroicon-s-variable',
            'heroicon-s-video-camera-slash',
            'heroicon-s-video-camera',
            'heroicon-s-view-columns',
            'heroicon-s-viewfinder-circle',
            'heroicon-s-wallet',
            'heroicon-s-wifi',
            'heroicon-s-window',
            'heroicon-s-wrench-screwdriver',
            'heroicon-s-wrench',
            'heroicon-s-x-circle',
            'heroicon-s-x-mark',
        ];

        return collect($heroicons)->mapWithKeys(function ($heroicon) {
            return [$heroicon => $heroicon];
        });
    }

    public static function getAppColors(): array
    {
        return [
            'primary' => 'Primary',
            'success' => 'Success',
            'danger' => 'Danger',
            'warning' => 'Warning',
            'info' => 'Info',
            'gray' => 'Gray',
        ];
    }

    public static function classHasTrait(object | string $classInstance, string $trait): bool
    {
        return in_array($trait, class_uses_recursive($classInstance));
    }

    public static function getEligibleParentComponents(): Collection
    {
        $all = VisualFormComponent::query()->where('is_active', '=', true)
            ->get();

        return $all->filter(function (VisualFormComponent $component) {
            $class = Utils::instantiateClass($component->getAttribute('component_type'));

            return $class->isLayout() || $class->hasChildren();
        })->mapWithKeys(function (VisualFormComponent $component) {
            $class = Utils::instantiateClass($component->getAttribute('component_type'));

            return [$component->getAttribute('id') => "{$component->getAttribute('label')} ({$class->getOptionName()})"];
        });
    }
}
