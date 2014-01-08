(function($) {

    // Submitted by tigress298 on Tue, 02/09/2010 - 19:15
    $.registerLiquidCanvasPlugin({
        name: "partialRoundedRect",
        defaultOpts: { radius:10, tl:"true", tr:"true", bl:"true", br:"true" },
        paint: function(area) {
            var ctx = area.ctx;
            var opts = this.opts;
            ctx.beginPath();
            ctx.moveTo(0, opts.radius);
            if (opts.bl == "true") {
                ctx.lineTo(0, area.height - opts.radius);
                ctx.quadraticCurveTo(0, area.height, opts.radius, area.height);
            }
            else {
                ctx.lineTo(0, area.height);
            }
            if (opts.br == "true") {
                ctx.lineTo(area.width - opts.radius, area.height);
                ctx.quadraticCurveTo(area.width, area.height, area.width, area.height - opts.radius);
            }
            else {
                ctx.lineTo(area.width, area.height);
            }
            if (opts.tr == "true") {
                ctx.lineTo(area.width, opts.radius);
                ctx.quadraticCurveTo(area.width, 0, area.width - opts.radius, 0);
            }
            else {
                ctx.lineTo(area.width, 0);
            }
            if (opts.tl == "true") {
                ctx.lineTo(opts.radius, 0);
                ctx.quadraticCurveTo(0, 0, 0, opts.radius);
            }
            else {
                ctx.lineTo(0, 0);
                ctx.lineTo(0, opts.radius);
            }
            ctx.closePath();
            if (this.action) this.action.paint(area); // for chaining
        }
    });

    $.registerLiquidCanvasPlugin({
        name: "gradient",
        defaultOpts: { from: "#fff", to:"#666", direction:"v" },
        paint: function(area) {
            var grad;
            if (this.opts.direction == "h") {
                grad = area.ctx.createLinearGradient(0, 0, area.width, 0);
            }
            else if (this.opts.direction == "d") {
                grad = area.ctx.createLinearGradient(0, 0, area.width, area.height);
            }
            else {
                grad = area.ctx.createLinearGradient(0, 0, 0, area.height);
            }
            grad.addColorStop(0, this.opts.from);
            grad.addColorStop(1, this.opts.to);
            area.ctx.fillStyle = grad;
            this.action.paint(area);
            area.ctx.fill();
        }
    });

})(jQuery);
