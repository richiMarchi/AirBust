$('document').ready(function () {
  updateSeats();
});

function updateSeats() {
  $.post("update.php", function (data) {
    let parsedData = JSON.parse(data);
    if (!parsedData.successful) {
      if (parsedData.msg === 'SERVER_ERROR') {
        alert("Server error!");
      } else {
        alert("Session timed out. Please log in...");
        $(location).attr('href', 'index.php');
      }
      return;
    }
    let myMail = parsedData.userReq;
    let totSeats = parseInt($('#totSeats').html());
    let purchSeats = parsedData.reservations.filter(function (x) {
      return x['status'] == 1;
    }).length;
    let resSeats = Object.keys(parsedData.reservations).length - purchSeats;
    let freeSeats = totSeats - purchSeats - resSeats;
    $('#purchSeats').html(purchSeats);
    $('#resSeats').html(resSeats);
    $('#freeSeats').html(freeSeats);

    for (var i = 1; i <= parsedData.rows; i++) {
      for (var j = 'A'.charCodeAt(0); j < 'A'.charCodeAt(0) + parsedData.seats_per_row; j++) {
        let id = String.fromCharCode(j) + i;
        let reservation = parsedData.reservations.filter(function (x) {
          return x['seat'] == id;
        });
        if (reservation.length) {
          if (reservation[0]['status'] == 1) {
            setStatus(id, 'purchased');
          } else {
            if (reservation[0]['user'] == myMail) {
              setStatus(id, 'personalReserved');
            } else {
              setStatus(id, 'reserved');
            }
          }
        } else {
          setStatus(id, 'available');
        }
      }
    }
    if ($('.personalReserved').length === 0) {
      $('#buyButton').hide();
    } else {
      $('#buyButton').show();
    }
  });
}

function seatClicked(seat) {
  let id = $(seat).attr('id');
  if ($("#" + id).hasClass('personalReserved')) {
    $.post("seat_clicked.php", {seat: id, action: "delete"}, function (data) {
      if (!data) {
        alert("Please log in before reserving seats");
        return;
      }
      let parsedData = JSON.parse(data);
      let isSuccessful = parsedData.successful;
      let msg = parsedData.msg;
      if (isSuccessful) {
        setStatus(id, 'available');
        $('#resSeats').html(parseInt($('#resSeats').html()) - 1);
        $('#freeSeats').html(parseInt($('#freeSeats').html()) + 1);
        if ($('.personalReserved').length === 0) {
          $('#buyButton').hide();
        } else {
          $('#buyButton').show();
        }
        alert("Seat unreserved");
      } else {
        if (msg === 'SESSION_TIMEOUT') {
          alert("Session timed out! Please log in...");
          $(location).attr('href', 'index.php');
        } else if (msg === 'THREATENING_INPUT') {
          alert("Threatening input.");
        } else if (msg === 'SERVER_ERROR') {
          alert("Server error!");
        }
      }
    });
  } else if ($("#" + id).hasClass('purchased')) {
    return;
  } else {
    $.post("seat_clicked.php", {seat: id, action: "reserve"}, function (data) {
      if (!data) {
        alert("Please log in before reserving seats");
        return;
      }
      let parsedData = JSON.parse(data);
      let isSuccessful = parsedData.successful;
      let msg = parsedData.msg;
      if (isSuccessful) {
        if ($("#" + id).hasClass('available')) {
          $('#resSeats').html(parseInt($('#resSeats').html()) + 1);
          $('#freeSeats').html(parseInt($('#freeSeats').html()) - 1);
        }
        setStatus(id, 'personalReserved');
        if (msg === 'RESERVE_OK') {
          alert('Free seat reserved');
        } else if (msg === 'RESERVE_STEAL') {
          alert('Previously occupied seat reserved');
        }
      } else {
        if (msg === 'RANGE_ERROR') {
          alert("Seat ID is not in the right range!");
        } else if (msg === 'SERVER_ERROR') {
          alert("Server error!");
        } else if (msg === 'SESSION_TIMEOUT') {
          alert("Session timed out! Please log in...");
          $(location).attr('href', 'index.php');
        } else if (msg === 'THREATENING_INPUT') {
          alert("Threatening input.");
        } else if (msg === 'RESERVE_FAIL') {
          alert('Seat has already been purchased!');
          $('#purchSeats').html(parseInt($('#purchSeats').html()) + 1);
          if ($("#" + id).hasClass('reserved')) {
            $('#resSeats').html(parseInt($('#resSeats').html()) - 1);
          } else {
            $('#freeSeats').html(parseInt($('#freeSeats').html()) - 1);
          }
          setStatus(id, 'purchased');
        }
      }
      if ($('.personalReserved').length === 0) {
        $('#buyButton').hide();
      } else {
        $('#buyButton').show();
      }
    });
  }
}

function buySeats() {
  let seatsToPurchase = 0;
  $(".personalReserved").each(function () {
    seatsToPurchase++;
  });
  $.post("buy_seats.php", {seats: seatsToPurchase}, function (data) {
    let parsedData = JSON.parse(data);
    let isSuccessful = parsedData.successful;
    let msg = parsedData.msg;
    if (isSuccessful) {
        alert("Seats successfully purchased!");
    } else {
      if (msg === 'SESSION_TIMEOUT') {
        alert("Session timed out! Please log in...");
        $(location).attr('href', 'index.php');
      } else if (msg === 'THREATENING_INPUT') {
        alert("Threatening input.");
      } else if (msg === 'SERVER_ERROR') {
        alert("Server error!");
      } else if (msg === 'PURCHASE_FAIL') {
        alert("Purchase failed. Someone reserved or purchased one or more of your seats.");
      }
    }
    $(location).attr('href', 'index.php');
  });
}

function setStatus(id, status) {
  $("#" + id).removeClass();
  $('#' + id).addClass(status);
}

function homepage() {
  $(location).attr('href', 'index.php');
}

function logout() {
  $(location).attr('href', 'logout.php');
}

function authenticationForm() {
  $(location).attr('href', 'authentication_form.php');
}