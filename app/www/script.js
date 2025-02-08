const STATUS = Object.freeze({
  WON: {
    status: "won",
    message: "Congratulations, You won!",
    form: () => {
      return createUserForm()
    },
  },
  LOST: { status: "lost", message: "You lost :(" },
  DRAW: { status: "draw", message: "Draw! Better luck next time." },
})

const GameTime = {
  start: 0,
  end: 0,
}

function makeMove(buttonId) {
  setButtonsValue(buttonId, "X")

  if (GameTime.start === 0) {
    GameTime.start = performance.now()
  }
  makeOpponentsTurn()
}

function makeOpponentsTurn() {
  const matrix = []

  let row = 1
  let col = 1
  let rowTexts = []
  do {
    const buttonId = `game_grid_${row}_${col}`

    // Very end of the matrix.
    if (document.getElementById(buttonId) == null && col === 1) {
      break
    }

    // End of the row.
    if (document.getElementById(buttonId) == null) {
      matrix.push(rowTexts)
      row++
      col = 1
      rowTexts = []
      continue
    }

    rowTexts.push(document.getElementById(buttonId).innerText)
    col++
  } while (true)

  fetch("/index/opponents-turn", {
    method: "POST",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ matrix: matrix }),
  })
    .then((response) => {
      if (response.ok) {
        return response.json()
      }
      return Promise.reject(response) // 2. reject instead of throw
    })
    .then((json) => {
      let is_game_over = json.is_game_over
      let is_player_win = json.is_player_win
      let is_computer_win = json.is_computer_win

      if (!is_game_over || !is_player_win) {
        let row = json.row + 1
        let col = json.col + 1
        const buttonId = `game_grid_${row}_${col}`
        setButtonsValue(buttonId, "O")
      }

      if (is_game_over) {
        GameTime.end = performance.now()

        document.querySelectorAll("#game_grid button").forEach((button) => {
          button.disabled = true
        })

        const statusObject = is_player_win
          ? STATUS.WON
          : is_computer_win
          ? STATUS.LOST
          : STATUS.DRAW

        showGameOverModal(statusObject)
      }
    })
}

function setButtonsValue(buttonId, text) {
  document.getElementById(buttonId).innerText = text
  if (text === "X") {
    document.getElementById(buttonId).classList.add("player")
  } else {
    document.getElementById(buttonId).classList.add("computer")
  }
  document.getElementById(buttonId).disabled = true
}

function showGameOverModal(gameStatus) {
  const modal = document.getElementById("gameOverModal")
  const backdrop = document.getElementById("backdrop")

  if (modal === null) {
    alert(gameStatus.message)
    // ToDo: I imagine we have a telemetry system for the front-end as well
    // ToDo: Here would be a good place to let our front-end engineers know we could not find the modal.
  }

  backdrop.style.display = "block"
  modal.style.display = "block"
  modal.classList.add("show")

  modal.querySelector(".modal-title").innerHTML = gameStatus.message

  if (gameStatus.status === "won") {
    modal.querySelector(".modal-body").appendChild(gameStatus.form())

    document
      .querySelector("input[name=userName]")
      .addEventListener("input", (event) => {
        event.preventDefault()
        const validation = validateInput(event.target.value)
        const submitBtn = document.getElementById("submitBtn")

        if (validation) {
          submitBtn.removeAttribute("disabled")
          submitBtn.addEventListener("click", submitForm)
        } else {
          submitBtn.setAttribute("disabled", "disbaled")
          submitBtn.removeEventListener("click", submitForm)
        }
      })
  }

  modal.querySelector("#closeModal").addEventListener("click", function () {
    closeGameModal(backdrop, modal)
  })
}

function closeGameModal(backdrop, modal) {
  backdrop.style.display = "none"
  modal.style.display = "none"
  modal.classList.remove("show")
}

function createUserForm() {
  const form = document.createElement("form")
  form.classList.add("form")

  const inputGroup = document.createElement("div")
  inputGroup.classList.add("form-group")

  const labelField = document.createElement("label")
  labelField.setAttribute("for", "userName")
  labelField.classList.add("userFieldLabel")

  const textContent = document.createTextNode(
    "Enter you game tag and see if you made it to the leaderboard"
  )

  const inputField = document.createElement("input")
  inputField.classList.add("form-control")
  inputField.setAttribute("type", "text")
  inputField.setAttribute("placeholder", "Add your username")
  inputField.setAttribute("name", "userName")

  const errorMessage = document.createElement("p")
  errorMessage.setAttribute("id", "userErrorMessage")
  errorMessage.classList.add("red-text")

  const csrfField = document.createElement("input")
  csrfField.setAttribute("name", "csrfToken")
  csrfField.setAttribute("type", "hidden")
  csrfField.setAttribute(
    "value",
    document.querySelector("meta[name=csrf-token]").getAttribute("content")
  )

  const gridSizeField = document.createElement("input")
  gridSizeField.setAttribute("name", "gridSize")
  gridSizeField.setAttribute("type", "hidden")
  gridSizeField.setAttribute(
    "value",
    document.getElementById("grid_size").getAttribute("value")
  )

  const playTimeField = document.createElement("input")
  playTimeField.setAttribute("name", "playTime")
  playTimeField.setAttribute("type", "hidden")
  playTimeField.setAttribute("value", GameTime.end - GameTime.start)

  labelField.appendChild(textContent)
  inputGroup.appendChild(labelField)
  inputGroup.appendChild(inputField)
  inputGroup.appendChild(errorMessage)

  form.appendChild(inputGroup)
  form.appendChild(csrfField)
  form.appendChild(gridSizeField)
  form.appendChild(playTimeField)

  return form
}

function validateInput(name) {
  if (name === "") {
    setErrorMessage("Field can't be empty")
    return false
  }

  if (name.length < 3 || name.length > 20) {
    setErrorMessage("A name must be at least 3 characters and maximum of 20")
    return false
  }
  // LOL did not know support for internationalization was this bad in JS :D
  if (!name.match(/^[a-zA-ZäöåÄÖÅ0-9_]+$/)) {
    setErrorMessage("only letters, numbers, and underscores allowed")
    return false
  }
  setErrorMessage("")
  return true
}

function setErrorMessage(message) {
  const elem = document.getElementById("userErrorMessage")

  elem.innerHTML = message
}

async function submitForm(event) {
  const name = document.querySelector("input[name=userName]").value
  const csrf = document.querySelector("input[name=csrfToken]").value
  const grid_size = document.querySelector("input[name=gridSize]").value
  const play_time = document.querySelector("input[name=playTime]").value

  response = await fetch("/leaderboard/create", {
    method: "POST",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      name: name,
      csrf: csrf,
      grid_size: grid_size,
      play_time: play_time,
    }),
  })

  if (response.status === 200) {
    json = await response.json()

    /**
     * Go to leaderboard with grid_size as param
     */
    window.location.href = `/leaderboard?grid_size=${json.grid_size}`
  }
}
