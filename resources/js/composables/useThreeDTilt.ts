import { ref, onMounted, onUnmounted, type Ref } from 'vue';

interface TiltOptions {
    max?: number;
    perspective?: number;
    scale?: number;
    speed?: number;
}

export function useThreeDTilt(options: TiltOptions = {}) {
    const { max = 12, perspective = 1000, scale = 1.03, speed = 300 } = options;

    const targetEl = ref<HTMLElement | null>(null);
    const transformStyle = ref<string>('');
    const transitionStyle = ref<string>(
        `transform ${speed}ms cubic-bezier(0.25, 1, 0.5, 1)`,
    );

    const handleMouseMove = (e: MouseEvent) => {
        if (!targetEl.value) {
            return;
        }

        const el = targetEl.value;
        const rect = el.getBoundingClientRect();

        // Mouse coordinates relative to target element
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        // Normalize mouse coordinates from -0.5 to 0.5
        const normalizedX = x / rect.width - 0.5;
        const normalizedY = y / rect.height - 0.5;

        // Calculate rotation angles
        const rotateX = -normalizedY * max;
        const rotateY = normalizedX * max;

        transformStyle.value = `perspective(${perspective}px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(${scale}, ${scale}, ${scale})`;
        transitionStyle.value = 'transform 80ms linear';
    };

    const handleMouseEnter = () => {
        transitionStyle.value = 'transform 80ms linear';
    };

    const handleMouseLeave = () => {
        transformStyle.value = `perspective(${perspective}px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)`;
        transitionStyle.value = `transform ${speed}ms cubic-bezier(0.25, 1, 0.5, 1)`;
    };

    onMounted(() => {
        if (targetEl.value) {
            targetEl.value.addEventListener('mousemove', handleMouseMove);
            targetEl.value.addEventListener('mouseenter', handleMouseEnter);
            targetEl.value.addEventListener('mouseleave', handleMouseLeave);
        }
    });

    onUnmounted(() => {
        if (targetEl.value) {
            targetEl.value.removeEventListener('mousemove', handleMouseMove);
            targetEl.value.removeEventListener('mouseenter', handleMouseEnter);
            targetEl.value.removeEventListener('mouseleave', handleMouseLeave);
        }
    });

    return {
        targetEl,
        transformStyle,
        transitionStyle,
    };
}
