<?php
/**
 * Encoded: the link comes from an external API.
 *
 * @var string $scriptLink
 */
?>
<script data-noptimize="1" data-cfasync="false" data-wpfc-render="false">
    (function () {
        var script = document.createElement("script");
        script.async = 1;
        script.src = <?= wp_json_encode($scriptLink) ?>;
        document.head.appendChild(script);
    })();
</script>
