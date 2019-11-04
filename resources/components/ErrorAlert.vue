<template>
    <div v-if="message" class="row">
      <div class="col-12">
        <div class="alert alert-danger" role="alert">
          <span v-html="message"></span>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div>
    </div>
</template>

<script>
export default {
  name: 'ErrorAlert',
  props: {
    message: {
      type: String,
      required: false,
      default: ''
    }
  }
};
</script>
