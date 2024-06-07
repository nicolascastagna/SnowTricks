const addVideoButton = document.getElementById("add-video");
const videoList = document.getElementById("videos-list");

document.addEventListener("DOMContentLoaded", function () {
    addVideoButton.addEventListener("click", () => {
        const index = videoList.querySelectorAll("input").length;
        const newWidget = videoList.dataset.prototype.replace(
            /__name__/g,
            index
        );

        const newVideoElement = document.createElement("div");
        newVideoElement.innerHTML = newWidget;

        const videoInputs = newVideoElement.querySelectorAll("input");
        videoInputs.forEach((input) => {
            input.style.marginBottom = "10px";
        });

        videoList.insertAdjacentElement("beforeend", newVideoElement);
    });
});

function scrollToTricks() {
    const tricksContainer = document.getElementById("tricks-container");
    tricksContainer.scrollIntoView({ behavior: "smooth" });
}

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: "smooth" });
}

document.addEventListener("DOMContentLoaded", function () {
    window.addEventListener("scroll", () => {
        const tricksContainer = document.getElementById("tricks-container");
        const scrollUpArrow = document.querySelector(".scroll-up-arrow");

        if (tricksContainer.getBoundingClientRect().top >= 0) {
            scrollUpArrow.style.display = "none";
        } else {
            scrollUpArrow.style.display = "block";
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const loadMoreBtn = document.getElementById("loadMoreTricks");
    const hiddenTricks = document.getElementById("hiddenTricks");

    loadMoreBtn.addEventListener("click", function () {
        hiddenTricks.style.display = "flex";
        loadMoreBtn.style.display = "none";
    });
});
