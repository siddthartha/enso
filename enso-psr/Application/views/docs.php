<?php
/** @var string $html */
?>
<html lang="en">
<head><title>Docs</title>
    <meta name="color-scheme" content="light dark" />
    <link rel="stylesheet" href="http://markdowncss.github.io/retro/css/retro.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flexboxgrid/6.3.1/flexboxgrid.min.css" type="text/css" />
<!--    <link rel="stylesheet" href="https://sindresorhus.com/github-markdown-css/github-markdown.css" type="text/css" /> -->
    <style>
        body {
            font-family: monospace;
            margin: 1rem 0 1rem 0;
            padding: 18px;
            max-width: fit-content;
        }
        canvas#canvas {
            left: 0;
            top: 0;
            position: absolute!important;
            z-index: 1;
        }

        code {
            padding: .2em .4em;
            margin: 0;
            font-size: 85%;
            background-color: #333;
            border-radius: 5px;
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
<div class="row around-xs">
    <div class="col-xs-10">
        <div class="markdown-body box"><?= $html; ?></div>
        <div class="markdown-body box">
            <h4>Donate my work (USDT / TRC20)</h4>
            <img alt="USDT/TRC20" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAACWAQMAAAAGz+OhAAAABlBMVEX///8AAABVwtN+AAABL0lEQVR42uzUMYoDMQwFUIEKX0rga6kIyDCFr2XQpVwI/qLZzO6mjWa7KG7yGnukj+hT/1EGbCUlxupAlE3zqFL3ECKpm21tux3wkB73mA216Os+m4iOm4xItU04Xvvyrp0zUn2IyOvc3rTzHqUH//lfMc0u5JszQkvKZthkg4jhHj3KRqo2d8sJ+XVHxdoc1OYcHXBfVDZtADAPd1/8xIo1jAxRCK8eHGWjtlXb4GzCT19utgzBIDUPBp59qZjmlxiC3f3CkrWdykHswXWjNrfNjLz0RVK3fDRwZAzgUTbDzN+xOPO1yva9K2cICQnX7dxrdmZI+pKy6TkmtdyR1x0ls61qCOHfrVu1BoyeuuQWI7IjSDKVdctjm+FYd9g5o9wRIRx9le1ThfoKAAD//yQvhLTrCA8oAAAAAElFTkSuQmCC" />
        </div>
    </div>
</div>
<canvas id="canvas"></canvas>
    <script>
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        // fill backgrund

        //ctx.fillStyle = 'transparent';
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Constants
        const X0 = canvas.width / 2;
        const Y0 = canvas.height / 2;
        const PEE = Math.PI;

        // State
        let stars = [];
        let numStars = 10000;
        let radius = 6000;
        let autoRotate = true;
        let centerX = X0;
        let centerY = Y0;

        // Mouse control
        let isDragging = false;
        let lastMouseX = 0;
        let lastMouseY = 0;
        let rotationSpeed = 0.01;

        let time = 0;

        // Star class
        class Star {
            constructor(x, y, z, speed) {
                this.xsign = 1;//(Math.random() > 0.5 ? 1 : -1);
                this.ysign = 1;//(Math.random() > 0.5 ? 1 : -1);
                this.origX = x;
                this.origY = -y; // Pascal code inverts Y
                this.origZ = z;
                this.x = x;
                this.y = -y;
                this.z = z;
                this.r = Math.sqrt(x*x + y*y + z*z);
                this.speed = speed;
                this.c = 0xff - 0x22;

                // Calculate initial angles
                this.precompteAngles();
                
                // Precompute diagonals
                this.xzDiag = Math.sqrt(this.r*this.r - this.y*this.y);
                this.yzDiag = Math.sqrt(this.r*this.r - this.x*this.x);
            }

            precompteAngles() {
                if (this.x > 0) {
                    this.alpha = Math.atan(this.z / this.x);
                } else {
                    this.alpha = PEE + Math.atan(this.z / this.x);
                }
                if (this.z > 0) {
                    this.gamma = Math.atan(this.y / this.z);
                } else {
                    this.gamma = PEE + Math.atan(this.y / this.z);
                }
            }

            move() {
                this.hide();

                if (autoRotate) {
                    this.alpha += this.speed * this.xsign;
                    this.x = this.xzDiag * Math.cos(this.alpha);
                    this.z = this.xzDiag * Math.sin(this.alpha);
                    this.yzDiag = Math.sqrt(this.r*this.r - this.x*this.x);
                    
                    if (this.z > 0) {
                        this.gamma = Math.atan(this.y / this.z);
                    } else {
                        this.gamma = PEE + Math.atan(this.y / this.z);
                    }
                    
                    this.gamma += this.speed * this.ysign;
                    this.y = this.yzDiag * Math.sin(this.gamma);
                    this.z = this.yzDiag * Math.cos(this.gamma);
                    this.xzDiag = Math.sqrt(this.r*this.r - this.y*this.y);
                    
                    if (this.x > 0) {
                        this.alpha = Math.atan(this.z / this.x);
                    } else {
                        this.alpha = PEE + Math.atan(this.z / this.x);
                    }
                }

                this.show();
            }

            hide() {
                this.draw(0);
            }

            show() {
                this.draw(this.c);
            }

            draw(colorValue) {
                const screenX = centerX + Math.round(this.x);
                const screenY = centerY + Math.round(this.y);
                const size = 1;
                
                const intensity = Math.min(0xff, 0x22 + Math.round(colorValue / (0xff-0x22) * (this.z/4 + 150)));
                const intensityR = intensity + 15;
                const intensityG = intensity;
                const intensityB = intensity - 5;
                ctx.fillStyle = `rgb(${intensityR}, ${intensityG}, ${intensityB})`;
                ctx.beginPath();
                ctx.arc(screenX, screenY, size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        // Initialize stars randomly (like Pascal "Ball" mode)
        function initStars2() {
            stars = [];
            for (let i = 0; i < numStars; i++) {
                let y = Math.random() * 20;
                if (y > 10) y = -1; else y = 1;
                y = y * (Math.floor(Math.random() * (Math.floor(Math.sqrt(radius)) + 1)) + 1);

                let x = Math.random() * 20;
                if (x > 10) x = -1; else x = 1;
                x = x * (Math.floor(Math.random() * (Math.floor(Math.sqrt(radius)) + 1)) + 1);

                let z = Math.random() * 20;
                if (z > 10) z = -1; else z = 1;

                if (radius > x*x + y*y) {
                    z = Math.sqrt(radius - x*x - y*y) * z;
                    stars.push(new Star(x, y, z, 0.01));
                } else {
                    i--;
                }
            }
        }

        function initStars() {
            stars = [];
            for (let i = 0; i < numStars; i++) {
                let q = i % 100;
                let j = i / 100;

                let x = q * 5 - 250;
                let y = j * 5 - 250;
                let z = Math.floor(
                    Math.sin(Math.sqrt(x*x + y*y) / radius * 400) * 20
                    + Math.cos(Math.sqrt(x*x/2 + y*y/3) / radius * 200) * 20
                    + Math.cos(Math.sqrt((x-120)*(x-120)/120 + (y+120)*(y+120)/120) / radius * 400) * 120
                );

                stars.push(new Star(x*3, y*3, z*3, 0.003));
            }
        }


function randomNormal(mean = 0, std = 1) {
    let u = 0, v = 0;

    while (u === 0) u = Math.random();
    while (v === 0) v = Math.random();

    return (
        Math.sqrt(-2 * Math.log(u)) *
        Math.cos(2 * Math.PI * v)
    ) * std + mean;
}

function generateGalaxyPoints() {
    const arms = 5;         // число рукавов
    const spin = 1.1;       // закрутка
    const armSpread = 0.3; // ширина рукава (рад)
    const thickness = 15;  // толщина диска по Z
    const count = 10000;
    const radius = 1000;

    for (let i = 0; i < count; i++) {
        // больше плотности к центру
        const r = Math.pow(Math.random(), 2.5) * radius;

        // выбор рукава
        const arm = Math.floor(Math.random() * arms);

        // угол спирали
        let angle =
            (arm / arms) * Math.PI * 2 +
            (r / radius) * spin * Math.PI;

        // шум вокруг рукава
        angle += randomNormal(0, armSpread);

        const x = r * Math.cos(angle);
        const y = r * Math.sin(angle);
        const z = randomNormal(0, 10 + 1 / r * thickness * 100);

        stars.push(new Star(x, y, z, 0.003));
    }
}        


        // Animation loop
        function animate() {
            //ctx.fillStyle = 'transparent';
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            for (const star of stars) {
                star.move();
            }

            // Draw center marker
            // ctx.fillStyle = 'yellow';
            // ctx.fillRect(centerX - 2, centerY - 2, 4, 4);

            requestAnimationFrame(animate);
            time++;
        }

        // Mouse event handlers
        canvas.addEventListener('mousedown', (e) => {
            isDragging = true;
            lastMouseX = e.clientX;
            lastMouseY = e.clientY;
        });

        canvas.addEventListener('mouseup', () => {
            isDragging = false;
        });

        canvas.addEventListener('mouseleave', () => {
            isDragging = false;
        });

        canvas.addEventListener('mousemove', (e) => {
            if (!isDragging) return;

            const deltaX = - (e.clientX - lastMouseX);
            const deltaY = e.clientY - lastMouseY;

            // Rotate all stars based on mouse movement
            for (const star of stars) {
                star.hide();
                
                // Rotate around Y axis (horizontal mouse movement)
                star.alpha += deltaX * rotationSpeed;
                star.x = star.xzDiag * Math.cos(star.alpha);
                star.z = star.xzDiag * Math.sin(star.alpha);
                star.yzDiag = Math.sqrt(star.r*star.r - star.x*star.x);
                
                if (star.z > 0) {
                    star.gamma = Math.atan(star.y / star.z);
                } else {
                    star.gamma = PEE + Math.atan(star.y / star.z);
                }

                // Rotate around X axis (vertical mouse movement)
                star.gamma += deltaY * rotationSpeed;
                star.y = star.yzDiag * Math.sin(star.gamma);
                star.z = star.yzDiag * Math.cos(star.gamma);
                star.xzDiag = Math.sqrt(star.r*star.r - star.y*star.y);
                
                if (star.x > 0) {
                    star.alpha = Math.atan(star.z / star.x);
                } else {
                    star.alpha = PEE + Math.atan(star.z / star.x);
                }

                star.show();
            }

            lastMouseX = e.clientX;
            lastMouseY = e.clientY;
        });

        // Zoom with scroll wheel
        let scale = 1;
        canvas.addEventListener('wheel', (e) => {
            e.preventDefault();
            const zoomFactor = e.deltaY > 0 ? 0.95 : 1.05;
            scale *= zoomFactor;
            scale = Math.max(0.3, Math.min(3, scale));
            
            for (const star of stars) {
                star.hide();
                star.x = star.origX * scale;
                star.y = star.origY * scale;
                star.z = star.origZ * scale;
                star.r = Math.sqrt(star.x*star.x + star.y*star.y + star.z*star.z);
                star.xzDiag = Math.sqrt(star.r*star.r - star.y*star.y);
                star.yzDiag = Math.sqrt(star.r*star.r - star.x*star.x);
                star.show();
            }
        }, { passive: false });

        // Toggle auto-rotate with spacebar
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space') {
                autoRotate = !autoRotate;
            }
        });

        // Resize handler
        function resize() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            centerX = canvas.width / 2;
            centerY = canvas.height / 2;
        }

        // Initialize
        resize();
        window.addEventListener('resize', resize);
        //initStars();
        generateGalaxyPoints();
        animate();
    </script>

</body>
</html>
