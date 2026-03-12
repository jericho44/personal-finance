/* global google */
export function loadGoogleMaps(
    apiKey: string,
    version: string = 'weekly',
    libraries: string[] = []
): Promise<typeof google> {
    return new Promise((resolve, reject) => {
        const callbackName = '__googleMapsCallback__';
        const scriptId = 'google-maps-script';

        // Jika sudah dimuat sebelumnya, langsung resolve
        if (typeof window.google?.maps?.importLibrary === 'function') {
            return resolve(window.google);
        }


        // Cek apakah script sudah ada tapi belum loaded
        if (document.getElementById(scriptId)) {
            const waitForGoogle = () => {
                if (window.google && window.google.maps && typeof window.google.maps.importLibrary === 'function') {
                    return resolve(window.google);
                } else {
                    setTimeout(waitForGoogle, 100);
                }
            };
            return waitForGoogle();
        }

        // Bangun URL
        const params = new URLSearchParams({
            key: apiKey,
            v: version,
            callback: `google.maps.${callbackName}`,
        });

        if (libraries.length) {
            params.set('libraries', libraries.join(','));
        }

        // Buat script element
        const script = document.createElement('script');
        script.id = scriptId;
        script.src = `https://maps.googleapis.com/maps/api/js?${params}`;
        script.async = true;
        script.defer = true;

        // Pastikan deklarasi google ada di window
        if (!window.google) window.google = {} as typeof google;
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        if (!window.google.maps) window.google.maps = {} as any;

        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        (window.google.maps as any)[callbackName] = () => {
            resolve(window.google);
        };

        script.onerror = () =>
            reject(new Error('Gagal memuat Google Maps JavaScript API.'));

        document.head.appendChild(script);
    });
}
