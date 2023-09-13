

function startBomb() {
    /* globals anime */
    const totalDuration = 10000;

    anime({
        targets: "#sparkles",
        loop: true, // Activez la boucle
        direction: "alternate", // Alternez entre l'animation et la réversion
        transform: [
            { value: "scale(1)", duration: 500, easing: "easeInOutSine" }, // État initial
            { value: "scale(0)", duration: 500, easing: "easeInOutSine" }, // État final
        ],
        begin: () => {playSparkler()},
    });

    anime({
        targets: "#ember",
        loop: true, // Activez la boucle
        direction: "alternate", // Alternez entre l'animation et la réversion
        transform: [
            { value: "scale(2.1)", duration: 500, easing: "easeInOutSine" }, // État initial
            { value: "scale(1.4)", duration: 500, easing: "easeInOutSine" }, // État final
        ],
    });

    const timeline = anime.timeline();

    timeline.add({
        targets: "#fuse",
        strokeDashoffset: (target) => -target.getTotalLength(),
        duration: totalDuration,
        // ! have the stroke-dasharray match the length of the path to create the actual dashes
        begin: (animation) => {
            const target = animation.animatables[0].target;
            const length = target.getTotalLength();
            target.setAttribute("stroke-dasharray", length);
        },
        easing: "linear",
    });

    // animate the spark to follow the path dictated by #motion-path
    const motionPath = document.querySelector("#motion-path");
    const path = anime.path(motionPath);

    timeline.add(
        {
            targets: "#spark",
            translateX: path("x"),
            translateY: path("y"),
            rotate: path("angle"),
            duration: totalDuration,
            easing: "linear",
        },
        `-=${totalDuration}`
    );

    timeline.add({
        targets: "#spark",
        scale: 4.5,
        opacity: 0,
        duration: totalDuration * 0.05,
        easing: "easeInOutSine",
    });

    timeline.add(
        {
            targets: "#bomb",
            scale: 1.5,
            opacity: 0,
            duration: totalDuration * 0.06,
            delay: totalDuration * 0.01,
            easing: "easeInOutSine",
            begin: () => {playBomb()}
        },
        `-=${totalDuration * 0.05}`
    );

    timeline.add(
        {
            targets: "#rupee",
            scale: [0, 1.2],
            opacity: [0, 1],
            duration: totalDuration * 0.06,
            delay: totalDuration * 0.01,
            easing: "easeInOutSine",
        },
        `-=${totalDuration * 0.05}`
    );
}
