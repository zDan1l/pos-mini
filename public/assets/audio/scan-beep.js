/**
 * Scan Sound Effects Helper
 * Memutar suara untuk scan barcode/QR code, success, dan error
 */

class ScanSound {
    constructor() {
        this.audioContext = null;
        this.sounds = {
            beep: null,
            success: null,
            error: null
        };

        // Load audio files
        this.initAudio();
    }

    initAudio() {
        this.sounds.beep = new Audio('/assets/audio/yatta.mp3');
        this.sounds.beep.preload = 'auto';

        this.sounds.success = new Audio('/assets/audio/yatta.mp3');
        this.sounds.success.preload = 'auto';

        this.sounds.error = new Audio('/assets/audio/error.wav');
        this.sounds.error.preload = 'auto';
    }

    play(soundName = 'beep') {
        const audio = this.sounds[soundName];
        if (audio) {
            const sound = audio.cloneNode();
            sound.volume = 1;
            sound.play().catch(() => {
                // Fallback ke Web Audio API
                this.playWebAudioFallback(soundName);
            });
        } else {
            this.playWebAudioFallback(soundName);
        }
    }

    playBeep() {
        this.play('beep');
    }

    playSuccess() {
        this.play('success');
    }

    playError() {
        this.play('error');
    }

    playWebAudioFallback(soundName) {
        try {
            if (!this.audioContext) {
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            }

            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(this.audioContext.destination);

            let frequency = 1800;
            let duration = 0.15;

            switch (soundName) {
                case 'success':
                    // Ascending tones
                    frequency = 2000;
                    duration = 0.3;
                    break;
                case 'error':
                    // Low tone
                    frequency = 300;
                    duration = 0.25;
                    break;
                default:
                    // Beep
                    frequency = 1800;
                    duration = 0.15;
            }

            oscillator.frequency.value = frequency;
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0.3, this.audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + duration);

            oscillator.start(this.audioContext.currentTime);
            oscillator.stop(this.audioContext.currentTime + duration);

        } catch (error) {
            console.error('Error playing sound:', error);
        }
    }
}

// Export sebagai global
window.ScanSound = ScanSound;
// Alias untuk backward compatibility
window.ScanBeep = ScanSound;
