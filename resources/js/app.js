import './bootstrap';
import PullToRefresh from 'pulltorefreshjs';

document.addEventListener('DOMContentLoaded', () => {
    const isIOSStandalone = window.navigator.standalone === true || window.matchMedia('(display-mode: standalone)').matches;

    // On active le script UNIQUEMENT si l'utilisateur est en mode application iOS
    if (isIOSStandalone) {
        PullToRefresh.init({
            mainElement: 'body', // L'élément à glisser (souvent le body)
            onRefresh() {
                window.location.reload();
            },
            distThreshold: 60, // Distance en pixels pour déclencher
            distMax: 80,
            instructionsPullToRefresh: 'Glissez pour rafraîchir',
            instructionsReleaseToRefresh: 'Relâchez pour rafraîchir',
            instructionsRefreshing: 'Chargement...',
            classPrefix: 'ptr-', // Permet de personnaliser le CSS si besoin
        });
    }
});