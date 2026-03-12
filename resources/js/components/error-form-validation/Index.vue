<template>
    <div v-if="vfield?.$error" class="text-danger">
      <template v-for="error in vfield.$errors" :key="error.$uid">
        <div>{{ error.$message }}</div>
      </template>
    </div>
  </template>
  
  <script setup lang="ts">
  defineProps({
    vfield: {
      type: Object,
      required: true
    }
  })
  </script>
  