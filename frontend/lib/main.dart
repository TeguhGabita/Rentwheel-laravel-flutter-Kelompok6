import 'package:flutter/material.dart';
import 'screens/login_screen.dart';
import 'screens/user/home_screen.dart';

void main() {
  runApp(const RentWheelApp());
}

class RentWheelApp extends StatelessWidget {
  const RentWheelApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'RentWheel',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        useMaterial3: true,
        colorSchemeSeed: const Color(0xFFFBBF24),
      ),
      initialRoute: '/login',
      routes: {
        '/login': (context) => const LoginScreen(),
        '/home': (context) => const HomeScreen(),
      },
    );
  }
}