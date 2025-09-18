from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

# Room data with capacities
ROOMS = {
    "Auditorium": 200,
    "Classroom 301": 50,
    "Classroom 302": 70,
    "Classroom 401": 100,
    "Classroom 402": 20,
    "Seminar hall":150
}

@app.route('/recommend', methods=['POST'])
def recommend_room():
    try:
        data = request.get_json()
        students = int(data.get('students', 0))

        if students <= 0:
            return jsonify({"error": "Invalid number of students"}), 400

        # Find the smallest room that can accommodate the students
        recommended_room = None
        for room, capacity in sorted(ROOMS.items(), key=lambda x: x[1]):
            if capacity >= students:
                recommended_room = f"{room} (Capacity: {capacity})"
                break

        if not recommended_room:
            return jsonify({"error": "No suitable room available"}), 404

        return jsonify({"recommended_room": recommended_room})

    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)

