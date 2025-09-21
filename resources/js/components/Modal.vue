<template>
  <transition name="modal">
    <div v-if="modelValue" class="modal-backdrop">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <slot name="header"></slot>
          <button class="close-button" @click="$emit('update:modelValue', false)">&times;</button>
        </div>
        
        <div class="modal-body">
          <slot></slot>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'Modal',
  
  props: {
    modelValue: {
      type: Boolean,
      required: true
    }
  },
  
  emits: ['update:modelValue'],

  mounted() {
    document.body.addEventListener('keydown', this.handleEscape);
  },

  beforeUnmount() {
    document.body.removeEventListener('keydown', this.handleEscape);
  },

  methods: {
    handleEscape(e) {
      if (this.modelValue && e.key === 'Escape') {
        this.$emit('update:modelValue', false);
      }
    }
  }
};
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: rgba(0, 0, 0, 0.3);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 100;
}

.modal-container {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  padding: 1rem;
  border-bottom: 1px solid #ddd;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 1.25rem;
  font-weight: bold;
}

.modal-body {
  padding: 1rem;
}

.close-button {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0;
  color: #666;
}

.close-button:hover {
  color: #000;
}

/* Transition animations */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>