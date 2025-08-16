import { precaching } from 'workbox-precaching';
import { registerRoute } from 'workbox-routing';
import { NetworkFirst } from 'workbox-strategies';

precaching.precacheAndRoute(self.__WB_MANIFEST);

registerRoute(
    ({ request }) => request.destination === 'document',
    new NetworkFirst({
        cacheName: 'html-cache',
    })
);