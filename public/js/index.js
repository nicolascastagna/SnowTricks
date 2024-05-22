const addVideoButton = document.getElementById("add-video");
const videoList = document.getElementById("videos-list");

addVideoButton.addEventListener("click", () => {
    const index = videoList.querySelectorAll("input").length;
    const newWidget = videoList.dataset.prototype.replace(/__name__/g, index);

    const newVideoElement = document.createElement("div");
    newVideoElement.innerHTML = newWidget;

    const videoInputs = newVideoElement.querySelectorAll("input");
    videoInputs.forEach((input) => {
        input.style.marginBottom = "10px";
    });

    videoList.insertAdjacentElement("beforeend", newVideoElement);
});
