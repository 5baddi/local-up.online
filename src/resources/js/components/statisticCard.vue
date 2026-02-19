<template>
    <div class="card">
        <div class="card-body analytics-card">
            <div class="d-flex align-items-center">
                <div class="subheader">{{ title }}</div>
            </div>
            <div class="h1 mt-3 mb-3 text-center">{{ value }}</div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            title: String,
            value: Number
        },
        data() {
            return {
                title: '',
                value: 0
            };
        }
    }
</script>