<template>
    <small class="badge badge-pill badge-primary" id="royalBadge">
        {{ userPoints.shorthand }}
    </small>
</template>

<script>
    export default {
        name: "RoyaltyBadge",
        props: {
            userId: {
                required: true,
                type: Number
            },
            initialPoints: {
                required: true,
                type: String
            }
        },
        data() {
            return {
                userPoints: {
                    number: 0,
                    shorthand: this.initialPoints
                },
                point: {}
            }
        },
        mounted() {
            Echo.private(`users.${this.userId}`)
                .listen('.points-given', (e) => {
                    this.point = e.point
                    this.userPoints = e.user_points
                })
        }
    }
</script>

<style scoped>

</style>